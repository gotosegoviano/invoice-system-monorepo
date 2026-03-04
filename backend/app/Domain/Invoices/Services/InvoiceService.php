<?php

namespace App\Domain\Invoices\Services;

use App\Domain\Invoices\Services\InvoicePdfService;
use App\Domain\Invoices\Enums\InvoiceType;
use App\Domain\Invoices\Strategies\CalculationStrategy;
use App\Domain\Invoices\Strategies\ProductCalculationStrategy;
use App\Domain\Invoices\Strategies\ServiceCalculationStrategy;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;

class InvoiceService
{
    protected InvoicePdfService $pdf_service;

    public function __construct(InvoicePdfService $pdf_service)
    {
        $this->pdf_service = $pdf_service;
    }

    public function create(array $data): array
    {
        $items = $data['items'] ?? [];

        if (empty($items)) {
            throw new InvalidArgumentException('Invoice must contain at least one item.');
        }

        //$this->validateItemsStructure($items);

        // It doesn't matter if the company and customer already exist, we will just create new ones for simplicity. In a real application, you would likely want to check for existing records and reuse them.
        $company = Company::create($data['company']);
        $customer = Customer::create($data['customer']);

        $type = InvoiceType::from($data['type']);

        // Calculate totals using the appropriate strategy
        $totals = $this->calculate($data, $items);

        return DB::transaction(function () use ($data, $items, $totals, $company, $customer, $type) {

            $invoice = Invoice::create([
                'uuid' => Str::uuid(),
                'type' => $type->value,
                'invoice_date' => $data['invoice_date'] ?? now(),
                'due_date' => $data['due_date'],
                'company_id' => $company->id,
                'customer_id' => $customer->id,
                'comments' => $data['comments'] ?? null,
                'subtotal' => $totals['subtotal'],
                'discount_total' => $totals['discount_total'],
                'tax_total' => $totals['tax_total'],
                'total' => $totals['total'],
            ]);

            foreach ($items as $item) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'discount' => $item['discount'] ?? 0,
                    'tax_rate' => $data['tax_total'] ?? 0,
                ]);
            }

            $pdf_url = $this->pdf_service->generate($invoice);

            return [
                'invoice' => $invoice,
                'pdf_url' => $pdf_url,
            ];
        });
    }

    public function calculate(array $data, array $items): array
    {
        $strategy = $this->resolveStrategy(InvoiceType::from($data['type']));
        return $strategy->calculate($items, $data['tax_total'] ?? 0, $data['discount'] ?? 0);
    }

    private function resolveStrategy(InvoiceType $type): CalculationStrategy
    {
        return match ($type) {
            InvoiceType::SERVICE => new ServiceCalculationStrategy(),
            InvoiceType::PRODUCT => new ProductCalculationStrategy(),
        };
    }

    private function validateItemsStructure(array $items): void
    {
        foreach ($items as $item) {
            if (!isset($item['quantity'], $item['price'], $item['type'])) {
                throw new InvalidArgumentException('Invalid item structure.');
            }
        }
    }
}