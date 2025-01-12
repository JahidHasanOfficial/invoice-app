<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class InvoiceController extends Controller
{
   //api for invoice
//     public function index(){
//        try {
//               $invoices = Invoice::orderBy('id', 'desc')->get();
//               return response()->json([
//                     'status' => true,
//                     'message' => 'Invoices fetched successfully',
//                     'data' => $invoices
//               ]);
//          } catch (\Exception $e) {
//               return response()->json([
//                     'status' => false,
//                     'message' => 'Failed to fetch invoices',
//                     'error' => $e->getMessage()
//               ]);
//          }
//     }

public function index(Request $request)
{
    try {
        // Start building the query
        $query = Invoice::with('customer'); // Include customer relationship

        // Optional filters (e.g., date range)
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        // Optional search (e.g., by invoice number or customer name)
        if ($request->has('search')) {
            $query->where('invoice_number', 'like', '%' . $request->search . '%')
                  ->orWhereHas('customer', function ($q) use ($request) {
                      $q->where('name', 'like', '%' . $request->search . '%');
                  });
        }

        // Fetch paginated results
        $invoices = $query->with('customer')->orderBy('id', 'desc')->paginate(10);

        // Return success response with data and pagination details
        return response()->json([
            'status' => true,
            'message' => 'Invoices fetched successfully',
            'data' => $invoices->items(), // Actual data
            'pagination' => [
                'current_page' => $invoices->currentPage(),
                'last_page' => $invoices->lastPage(),
                'total' => $invoices->total(),
                'per_page' => $invoices->perPage(),
            ]
        ], 200);
    } catch (\Exception $e) {
        // Log the error for debugging
        Log::error('Failed to fetch invoices: ' . $e->getMessage());

        // Return error response
        return response()->json([
            'status' => false,
            'message' => 'Failed to fetch invoices',
            'error' => $e->getMessage()
        ], 500);
    }
}

}
