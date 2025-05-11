<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use App\Models\InvoiceLine;
use App\Services\AssistantService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MakeAIInvoiceCommand extends Command
{
    protected $signature = 'make:ai-invoice {description : The Thai description of the invoice}'
    protected $description = 'Generate an invoice from a Thai language description using AI';

    public function handle(AssistantService $assistant)
    {
        try {
            $description = $this->argument('description');
            
            // Get AI response using the invoice creation prompt
            $response = $assistant->getResponse('invoice_create', [
                'description' => $description
            ]);
            
            // Decode the JSON response
            $invoiceData = json_decode($response, true);
            
            if (!$invoiceData) {
                throw new \RuntimeException('Invalid response format from AI assistant');
            }
            
            // Begin transaction
            DB::beginTransaction();
            
            // Create invoice
            $invoice = Invoice::create([
                'invoice_number' => $invoiceData['invoice_number'],
                'customer_id' => $invoiceData['customer_id'],
                'issue_date' => $invoiceData['issue_date'],
                'due_date' => $invoiceData['due_date'],
                'subtotal' => $invoiceData['subtotal'],
                'tax' => $invoiceData['tax'],
                'total' => $invoiceData['total'],
                'notes' => $description // Store original description
            ]);
            
            // Create invoice lines
            foreach ($invoiceData['lines'] as $line) {
                InvoiceLine::create([
                    'invoice_id' => $invoice->id,
                    'description' => $line['description'],
                    'quantity' => $line['quantity'],
                    'unit_price' => $line['unit_price'],
                    'tax_rate' => $line['tax_rate'],
                    'tax_amount' => $line['tax_amount'],
                    'subtotal' => $line['subtotal'],
                    'total' => $line['total']
                ]);
            }
            
            DB::commit();
            
            $this->info('Invoice created successfully!');
            $this->line('Invoice Number: ' . $invoice->invoice_number);
            
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Failed to create invoice: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}