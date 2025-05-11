<?php

namespace App\Observers;

use App\Jobs\GenerateInvoiceXML;
use App\Models\Invoice;

class InvoiceObserver
{
    public function created(Invoice $invoice): void
    {
        if ($invoice->status === 'issued') {
            GenerateInvoiceXML::dispatch($invoice);
        }
    }
}