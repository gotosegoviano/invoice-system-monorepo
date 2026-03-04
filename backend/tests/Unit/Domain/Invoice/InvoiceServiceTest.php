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

    public function test_it_creates_invoice_successfully_for_service_type()
    {
        $data = [
            'company' => [
                'name' => 'ACME Inc',
                'first_name' => 'Alice',
                'last_name' => 'Smith',
                'phone' => '1234567890',
                'web_page_url' => 'https://example.com',
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
            ],
            'type' => 'service'
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

    public function test_it_throws_exception_if_items_are_empty()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->service->create([
            'company' => ['name' => 'A', 'email' => 'a@test.com'],
            'customer' => ['name' => 'B', 'email' => 'b@test.com'],
            'items' => []
        ]);
    }

    public function test_it_throws_exception_if_item_structure_is_invalid()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->service->create([
            'company' => ['name' => 'A', 'first_name' => 'Alice', 'last_name' => 'Smith', 'phone' => '1234567890', 'web_page_url' => 'https://example.com', 'email' => 'a@test.com'],
            'customer' => ['name' => 'B', 'email' => 'b@test.com'],
            'items' => [],
            'due_date' => now()->addDays(7),
            'type' => 'service'
        ]);
    }
}