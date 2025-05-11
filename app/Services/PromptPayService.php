<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\PaymentIntent;
use Stripe\StripeClient;

class PromptPayService
{
    private StripeClient $stripe;

    public function __construct()
    {
        $this->stripe = new StripeClient(config('services.stripe.secret'));
    }

    public function createPaymentIntent(Invoice $invoice): PaymentIntent
    {
        $stripeIntent = $this->stripe->paymentIntents->create([
            'amount' => $invoice->total * 100, // Convert to smallest currency unit
            'currency' => 'thb',
            'payment_method_types' => ['promptpay'],
            'metadata' => [
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number
            ]
        ]);

        return PaymentIntent::create([
            'invoice_id' => $invoice->id,
            'stripe_payment_intent_id' => $stripeIntent->id,
            'status' => $stripeIntent->status,
            'amount' => $invoice->total,
            'currency' => 'thb',
            'metadata' => json_encode([
                'customer_name' => $invoice->customer->name,
                'invoice_number' => $invoice->invoice_number
            ])
        ]);
    }

    public function getQRCode(PaymentIntent $paymentIntent): string
    {
        $intent = $this->stripe->paymentIntents->retrieve($paymentIntent->stripe_payment_intent_id);
        return $intent->next_action->promptpay_display_qr_code->image_url_png;
    }

    public function handleWebhook(string $payload, string $sigHeader): void
    {
        $event = $this->stripe->webhooks->constructEvent(
            $payload,
            $sigHeader,
            config('services.stripe.webhook_secret')
        );

        if ($event->type === 'payment_intent.succeeded') {
            $paymentIntent = PaymentIntent::where('stripe_payment_intent_id', $event->data->object->id)->first();
            if ($paymentIntent) {
                $paymentIntent->update([
                    'status' => 'succeeded',
                    'paid_at' => now()
                ]);
            }
        }
    }
}