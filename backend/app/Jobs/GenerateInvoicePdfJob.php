<?php

namespace App\Jobs;

use App\Domain\Invoices\Services\InvoicePdfService;
use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateInvoicePdfJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $invoiceId;

    public function __construct(int $invoice_id)
    {
        $this->invoiceId = $invoice_id;
    }

    public function handle(InvoicePdfService $pdf_service): void
    {
        $invoice = Invoice::findOrFail($this->invoiceId);

        $pdf_path = $pdf_service->generate($invoice);

        $invoice->update([
            'pdf_path' => $pdf_path
        ]);
    }
}