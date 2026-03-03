<?php

namespace Tests\Unit\Application\Invoice;

use App\Domain\Invoice\Services\InvoicePdfService;
use App\Jobs\GenerateInvoicePdfJob;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Invoice;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GenerateInvoicePdfJobTest extends TestCase
{
    use RefreshDatabase;

    public function handle_stores_pdf()
    {
        $company = Company::factory()->create();
        $customer = Customer::factory()->create();

        $invoice = Invoice::factory()->create([
            'company_id' => $company->id,
            'customer_id' => $customer->id,
            'pdf_path' => null,
        ]);

        $pdfMock = Mockery::mock(InvoicePdfService::class);
        $pdfMock->shouldReceive('generate')
            ->once()
            ->andReturn('/storage/invoices/test.pdf');

        $job = new GenerateInvoicePdfJob($invoice->id);

        $job->handle($pdfMock);

        $this->assertDatabaseHas('invoices', [
            'id' => $invoice->id,
            'pdf_path' => '/storage/invoices/test.pdf'
        ]);
    }
}