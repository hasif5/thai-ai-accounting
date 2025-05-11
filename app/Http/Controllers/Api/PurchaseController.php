<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PurchaseController extends Controller
{
    public function index()
    {
        $purchases = Purchase::with(['supplier', 'purchaseLines'])->latest()->paginate();
        return response()->json($purchases);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:issue_date',
            'notes' => 'nullable|string',
        ]);

        $purchase = Purchase::create($validated);
        return response()->json($purchase, Response::HTTP_CREATED);
    }

    public function show(Purchase $purchase)
    {
        return response()->json($purchase->load(['supplier', 'purchaseLines']));
    }

    public function update(Request $request, Purchase $purchase)
    {
        $validated = $request->validate([
            'supplier_id' => 'sometimes|required|exists:suppliers,id',
            'issue_date' => 'sometimes|required|date',
            'due_date' => 'sometimes|required|date|after_or_equal:issue_date',
            'notes' => 'nullable|string',
        ]);

        $purchase->update($validated);
        return response()->json($purchase);
    }

    public function destroy(Purchase $purchase)
    {
        $purchase->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}