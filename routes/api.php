<?php

use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InvoiceController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//api for invoice
Route::get('/invoices', [InvoiceController::class, 'index']);   
Route::get('/create_invoice', [InvoiceController::class, 'create_invoice']);   
