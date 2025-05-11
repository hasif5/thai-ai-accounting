<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::with(['customer', 'invoiceLines'])->latest()->paginate();
        return response()->json($invoices);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:issue_date',
            'notes' => 'nullable|string',
        ]);

        $invoice = Invoice::create($validated);
        return response()->json($invoice, Response::HTTP_CREATED);
    }

    public function show(Invoice $invoice)
    {
        return response()->json($invoice->load(['customer', 'invoiceLines']));
    }

    public function update(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'customer_id' => 'sometimes|required|exists:customers,id',
            'issue_date' => 'sometimes|required|date',
            'due_date' => 'sometimes|required|date|after_or_equal:issue_date',
            'notes' => 'nullable|string',
        ]);

        $invoice->update($validated);
        return response()->json($invoice);
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}