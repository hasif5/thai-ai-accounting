<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Models\Receipt;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ReceiptController extends Controller
{
    /**
     * Display a listing of the client's receipts.
     */
    public function index(Request $request)
    {
        $customer = $request->user('customer');

        // Get receipts for invoices belonging to this customer
        $receipts = Receipt::whereHas('invoice', function ($query) use ($customer) {
            $query->where('customer_id', $customer->id);
        })
            ->with('invoice')
            ->latest()
            ->paginate(10);

        return response()->json($receipts);
    }

    /**
     * Display the specified receipt.
     */
    public function show(Request $request, $id)
    {
        $customer = $request->user('customer');

        // Find receipt and ensure it belongs to an invoice owned by this customer
        $receipt = Receipt::whereHas('invoice', function ($query) use ($customer) {
            $query->where('customer_id', $customer->id);
        })
            ->with('invoice')
            ->findOrFail($id);

        return response()->json($receipt);
    }
}