<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Domain\Invoices\Services\InvoiceService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class InvoiceController extends Controller
{

    public function store(Request $request, InvoiceService $invoice_service): JsonResponse
    {
        $validated = $request->validate([
            'company.name' => 'required|string',
            'company.first_name' => 'required|string',
            'company.last_name' => 'required|string',
            'company.email' => 'required|email',
            'company.web_page_url' => 'required|url',
            'company.phone' => 'required|string',
            'customer.name' => 'required|string',
            'customer.email' => 'required|email',
            'due_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.tax_rate' => 'required|numeric|min:0',
            'items.*.type' => 'required|in:service,product',
        ]);

        $result = $invoice_service->create($validated);

        return response()->json([
            'message' => 'Invoice created successfully',
            'invoice_id' => $result['invoice']->id,
            'uuid' => $result['invoice']->uuid,
            'pdf_path' => $result['invoice']->pdf_path,
            'total' => $result['invoice']->total,
        ], 201);
    }
}