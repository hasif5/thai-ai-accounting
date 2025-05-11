<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Services\PromptPayService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class InvoiceController extends Controller
{
    protected $promptPayService;

    public function __construct(PromptPayService $promptPayService)
    {
        $this->promptPayService = $promptPayService;
    }

    public function index(Request $request)
    {
        $customer = $request->user('customer');
        $invoices = $customer->invoices()
            ->with('customer')
            ->latest()
            ->paginate(10);

        return response()->json($invoices);
    }

    public function show(Request $request, $id)
    {
        $customer = $request->user('customer');
        $invoice = $customer->invoices()
            ->with(['lines'])
            ->findOrFail($id);

        return response()->json($invoice);
    }

    public function payment(Request $request, $id)
    {
        $customer = $request->user('customer');
        $invoice = $customer->invoices()->findOrFail($id);

        if ($invoice->status === 'paid') {
            return response()->json([
                'message' => 'Invoice is already paid'
            ]);
        }

        // Create or retrieve payment intent
        $paymentIntent = $invoice->paymentIntent ?? $this->promptPayService->createPaymentIntent($invoice);

        // Get QR code
        $qrImageUrl = $this->promptPayService->getQRCode($paymentIntent);

        return response()->json([
            'qr_image_url' => $qrImageUrl,
            'payment_intent_id' => $paymentIntent->id
        ]);
    }

    public function dashboard(Request $request)
    {
        $customer = $request->user('customer');

        // Get outstanding invoices
        $outstandingInvoices = $customer->invoices()
            ->where('status', 'issued')
            ->get();

        $outstandingCount = $outstandingInvoices->count();
        $outstandingTotal = $outstandingInvoices->sum('total');

        // Get last payment
        $lastPayment = $customer->payments()
            ->latest()
            ->first();

        $lastPaymentData = $lastPayment ? [
            'amount' => $lastPayment->amount,
            'date' => $lastPayment->created_at,
            'invoice_number' => $lastPayment->invoice->invoice_number
        ] : null;

        return response()->json([
            'outstanding_count' => $outstandingCount,
            'outstanding_total' => $outstandingTotal,
            'last_payment' => $lastPaymentData
        ]);
    }
}