<?php

namespace Tests\Unit\Domain\Invoice;

use App\Domain\Invoices\Services\InvoiceService;
use App\Domain\Invoices\Enums\InvoiceType;
use PHPUnit\Framework\TestCase;
use InvalidArgumentException;

class InvoiceServiceTest extends TestCase
{
    public function test_it_calculates_service_invoice(): void
    {
        $service = new InvoiceService();

        $items = [
            [
                'quantity' => 1,
                'price' => 100,
                'discount' => 10,
                'tax_rate' => 10,
            ],
        ];

        $result = $service->calculate(InvoiceType::SERVICE, $items);

        $this->assertEquals(99.00, $result['total']);
    }

    public function test_it_throws_exception_when_no_items(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $service = new InvoiceService();

        $service->calculate(InvoiceType::SERVICE, []);
    }
}