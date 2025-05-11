<?php

namespace App\Jobs;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use DOMDocument;
use DOMElement;

class GenerateInvoiceXML implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $queue = 'high';
    protected $invoice;

    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    public function handle(): void
    {
        if ($this->invoice->status !== 'issued') {
            return;
        }

        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;

        $root = $dom->createElement('TaxInvoice');
        $root->setAttribute('xmlns', 'urn:th:gov:rd:taxinvoice:1.0');
        $dom->appendChild($root);

        $this->addHeaderInfo($dom, $root);
        $this->addBuyerInfo($dom, $root);
        $this->addSellerInfo($dom, $root);
        $this->addInvoiceItems($dom, $root);
        $this->addSummary($dom, $root);

        $filename = 'invoices/xml/' . $this->invoice->invoice_number . '.xml';
        $path = storage_path('app/' . $filename);
        
        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        $dom->save($path);
        $this->invoice->update(['xml_path' => $filename]);
    }

    protected function addHeaderInfo(DOMDocument $dom, DOMElement $root): void
    {
        $header = $dom->createElement('Header');
        $root->appendChild($header);

        $header->appendChild($dom->createElement('InvoiceNumber', $this->invoice->invoice_number));
        $header->appendChild($dom->createElement('IssueDate', $this->invoice->issue_date));
        $header->appendChild($dom->createElement('Purpose', 'TIVC01'));
    }

    protected function addBuyerInfo(DOMDocument $dom, DOMElement $root): void
    {
        $buyer = $dom->createElement('Buyer');
        $root->appendChild($buyer);

        $customer = $this->invoice->customer;
        $buyer->appendChild($dom->createElement('Name', $customer->name));
        $buyer->appendChild($dom->createElement('TaxID', $customer->tax_id));
        $buyer->appendChild($dom->createElement('Address', $customer->address));
    }

    protected function addSellerInfo(DOMDocument $dom, DOMElement $root): void
    {
        $seller = $dom->createElement('Seller');
        $root->appendChild($seller);

        $company = config('company');
        $seller->appendChild($dom->createElement('Name', $company['name']));
        $seller->appendChild($dom->createElement('TaxID', $company['tax_id']));
        $seller->appendChild($dom->createElement('Address', $company['address']));
    }

    protected function addInvoiceItems(DOMDocument $dom, DOMElement $root): void
    {
        $items = $dom->createElement('Items');
        $root->appendChild($items);

        foreach ($this->invoice->lines as $line) {
            $item = $dom->createElement('Item');
            $items->appendChild($item);

            $item->appendChild($dom->createElement('Description', $line->description));
            $item->appendChild($dom->createElement('Quantity', number_format($line->quantity, 2)));
            $item->appendChild($dom->createElement('UnitPrice', number_format($line->unit_price, 2)));
            $item->appendChild($dom->createElement('Amount', number_format($line->subtotal, 2)));
            $item->appendChild($dom->createElement('TaxRate', number_format($line->tax_rate, 2)));
            $item->appendChild($dom->createElement('TaxAmount', number_format($line->tax_amount, 2)));
        }
    }

    protected function addSummary(DOMDocument $dom, DOMElement $root): void
    {
        $summary = $dom->createElement('Summary');
        $root->appendChild($summary);

        $summary->appendChild($dom->createElement('TotalAmount', number_format($this->invoice->subtotal, 2)));
        $summary->appendChild($dom->createElement('TaxAmount', number_format($this->invoice->tax, 2)));
        $summary->appendChild($dom->createElement('GrandTotal', number_format($this->invoice->total, 2)));
    }
}