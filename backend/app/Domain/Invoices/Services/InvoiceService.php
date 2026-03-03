<?php

namespace App\Domain\Invoices\Services;

use App\Domain\Invoices\Enums\InvoiceType;
use App\Domain\Invoices\Strategies\CalculationStrategy;
use App\Domain\Invoices\Strategies\ProductCalculationStrategy;
use App\Domain\Invoices\Strategies\ServiceCalculationStrategy;
use InvalidArgumentException;

class InvoiceService
{
    /**
     * Calculates the invoice totals based on the provided type and items.
     * Each item should have 'quantity', 'price', 'discount' (optional), and 'tax_rate'.
     * @param InvoiceType $type The type of invoice (product or service).
     * @param array $items An array of items, each containing 'quantity', 'price', 'discount' (optional), and 'tax_rate'.
     * @return array An array containing 'subtotal', 'discount_total', 'tax_total', and 'total'.
     * @throws InvalidArgumentException If the items array is empty or has an invalid structure.
     */
    public function calculate(InvoiceType $type, array $items): array
    {
        if (empty($items)) {
            throw new InvalidArgumentException('Invoice must contain at least one item.');
        }

        $this->validateItemsStructure($items);

        $strategy = $this->resolveStrategy($type);

        return $strategy->calculate($items);
    }

    /**
     * Resolves the appropriate calculation strategy based on the invoice type.
     * @param InvoiceType $type The type of invoice (product or service).
     * @return CalculationStrategy The corresponding calculation strategy instance.
     */
    private function resolveStrategy(InvoiceType $type): CalculationStrategy
    {
        return match ($type) {
            InvoiceType::SERVICE => new ServiceCalculationStrategy(),
            InvoiceType::PRODUCT => new ProductCalculationStrategy(),
        };
    }

    /**
     * Validates the structure of the items array to ensure it contains the required fields.
     * Each item must have 'quantity', 'price', and 'tax_rate'.
     * @param array $items The array of items to validate.
     * @throws InvalidArgumentException If any item is missing required fields.
     */
    private function validateItemsStructure(array $items): void
    {
        foreach ($items as $item) {
            if (
                !isset($item['quantity']) ||
                !isset($item['price']) ||
                !isset($item['tax_rate'])
            ) {
                throw new InvalidArgumentException('Invalid item structure.');
            }
        }
    }
}