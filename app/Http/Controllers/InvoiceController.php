<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
   //api for invoice
    public function index(){
       try {
              $invoices = Invoice::orderBy('id', 'desc')->get();
              return response()->json([
                    'status' => true,
                    'message' => 'Invoices fetched successfully',
                    'data' => $invoices
              ]);
         } catch (\Exception $e) {
              return response()->json([
                    'status' => false,
                    'message' => 'Failed to fetch invoices',
                    'error' => $e->getMessage()
              ]);
         }
    }
}
