<?php

namespace Tests\Unit\Domain\Invoices\Services;

use App\Domain\Invoices\Services\InvoicePdfService;
use App\Domain\Invoices\Services\InvoiceService;
use Tests\TestCase;
use Mockery;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Domain\Invoices\Enums\InvoiceType;
use App\Models\Invoice;

class InvoiceServiceTest extends TestCase
{
    use RefreshDatabase;

    protected InvoicePdfService $pdfMock;
    protected InvoiceService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pdfMock = Mockery::mock(InvoicePdfService::class);
        $this->service = new InvoiceService($this->pdfMock);
    }

    public function it_creates_invoice_successfully_for_service_type()
    {
        $data = [
            'company' => [
                'name' => 'ACME Inc',
                'email' => 'acme@test.com'
            ],
            'customer' => [
                'name' => 'John Doe',
                'email' => 'john@test.com'
            ],
            'due_date' => now()->addDays(7),
            'items' => [
                [
                    'description' => 'Consulting',
                    'quantity' => 2,
                    'price' => 100,
                    'discount' => 10,
                    'tax_rate' => 10,
                    'type' => 'service'
                ]
            ]
        ];

        $this->pdfMock
            ->shouldReceive('generate')
            ->once()
            ->andReturn('/storage/invoices/test.pdf');

        $result = $this->service->create($data);

        $this->assertDatabaseCount('companies', 1);
        $this->assertDatabaseCount('customers', 1);
        $this->assertDatabaseCount('invoices', 1);
        $this->assertDatabaseCount('invoice_items', 1);

        $this->assertEquals('/storage/invoices/test.pdf', $result['pdf_url']);
        $this->assertInstanceOf(Invoice::class, $result['invoice']);
    }

    public function it_throws_exception_if_items_are_empty()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->service->create([
            'company' => ['name' => 'A', 'email' => 'a@test.com'],
            'customer' => ['name' => 'B', 'email' => 'b@test.com'],
            'items' => []
        ]);
    }

    public function it_throws_exception_if_item_structure_is_invalid()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->service->create([
            'company' => ['name' => 'A', 'email' => 'a@test.com'],
            'customer' => ['name' => 'B', 'email' => 'b@test.com'],
            'items' => [
                [
                    'quantity' => 1,
                    'price' => 100,
                    // tax_rate missing
                    'type' => 'service'
                ]
            ]
        ]);
    }

    public function it_throws_exception_when_mixing_item_types()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->service->create([
            'company' => ['name' => 'A', 'email' => 'a@test.com'],
            'customer' => ['name' => 'B', 'email' => 'b@test.com'],
            'items' => [
                [
                    'description' => 'Item 1',
                    'quantity' => 1,
                    'price' => 100,
                    'tax_rate' => 10,
                    'type' => 'service'
                ],
                [
                    'description' => 'Item 2',
                    'quantity' => 1,
                    'price' => 100,
                    'tax_rate' => 10,
                    'type' => 'product'
                ]
            ]
        ]);
    }
}