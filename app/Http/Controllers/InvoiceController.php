<?php

namespace App\Http\Controllers;

use App\Models\Counter;
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
        // Start building the query and include customer relationship
        $query = Invoice::with('customer');

        // Optional filters for date range
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        // Optional search logic
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('number', 'like', "%$search%")
                  ->orWhere('due_date', 'like', "%$search%")
                  ->orWhere('id', 'like', "%$search%")
                  ->orWhere('date', 'like', "%$search%")
                  ->orWhere('total', 'like', "%$search%")
                  ->orWhere('invoice_number', 'like', "%$search%")
                  ->orWhereHas('customer', function ($subQuery) use ($search) {
                      $subQuery->where('name', 'like', "%$search%");
                  });
            });
        }

        // Fetch paginated results
        $invoices = $query->orderBy('id', 'desc')->paginate(10);

        // Return success response with data and pagination details
        return response()->json([
            'status' => true,
            'message' => 'Invoices fetched successfully',
            'data' => $invoices->items(),
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

public function create_invoice(Request $request){
      $counter = Counter::where('key', 'invoice')->first();
      $random = Counter::where('key', 'invoice')->first();

      $invoice = Invoice::orderBy('id', 'desc')->first();
      if ($invoice) {
        $invoice = $invoice->id + 1;
        $counters = $counter->value + $invoice;
      } else {
        $counters = $counter->value + 1;
      }
      $formData = [
        'number' => $counter->prefix . $counters,
        'customer_id' => null,
            'date' => date('Y-m-d'),
            'due_date' => null,
            'reference' => null,
            'discount' => 0,
            'terms_and_conditions' => 'Default terms and conditions',
            'items' => [
                  [
                        'product_id' => null,
                        'product' => null,
                        'quantity' => 1,
                        'unit_price' => 0,
                        'total' => 100
                  ]
                 
            ]
                  ];
                  return response()->json([
                        'status' => true,
                        'message' => 'Invoice created successfully',
                        'data' => $formData
                  ]);
}

}