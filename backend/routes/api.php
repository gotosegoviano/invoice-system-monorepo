<?php

use App\Http\Controllers\Api\InvoiceController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'status' => 'API running',
        'version' => '1.0.0'
    ]);
});

Route::post('/invoices', [InvoiceController::class, 'store']);
