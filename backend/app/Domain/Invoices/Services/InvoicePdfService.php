<?php

namespace App\Domain\Invoices\Services;

use App\Models\Invoice;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class InvoicePdfService
{
    public function generate(Invoice $invoice): string
    {
        $fileName = 'invoices/' . Str::uuid() . '.pdf';
        $pdf = Pdf::loadView('pdf.invoice', compact('invoice'))->output();
        Storage::disk('public')->put($fileName, $pdf);

        $pdf_path = url(Storage::url($fileName));

        $invoice->pdf_path = $pdf_path;
        ;
        $invoice->save();

        return $pdf_path;
    }
}