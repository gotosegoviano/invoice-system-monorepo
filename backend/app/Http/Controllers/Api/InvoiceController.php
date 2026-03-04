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
            'company.phone' => 'required|digits:10',
            'customer.name' => 'required|string',
            'due_date' => 'required|date',
            'invoice_date' => 'nullable|date',
            'comments' => 'nullable|string',
            'invoice_number' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'type' => 'required|in:service,product',
            'tax_total' => 'required|numeric|min:0',
            'discount_total' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
        ]);

        if ($request->hasFile('company.logo')) {
            $path = $request->file('company.logo')->store('logos', 'public');
            $validated['company']['logo_path'] = $path;
        }

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