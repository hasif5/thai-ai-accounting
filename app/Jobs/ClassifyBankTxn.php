<?php

namespace App\Jobs;

use App\Models\LedgerEntry;
use App\Services\AssistantService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ClassifyBankTxn implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private array $transaction;

    public function __construct(array $transaction)
    {
        $this->transaction = $transaction;
    }

    public function handle(AssistantService $assistant): void
    {
        $response = $assistant->chat('bank_classify', [
            'transaction_date' => $this->transaction['transaction_date'],
            'transaction_description' => $this->transaction['transaction_description'],
            'transaction_amount' => $this->transaction['transaction_amount'],
            'transaction_type' => $this->transaction['transaction_type']
        ]);

        $classification = json_decode($response, true);

        // Create ledger entry based on classification
        LedgerEntry::create([
            'date' => $this->transaction['transaction_date'],
            'description' => $this->transaction['transaction_description'],
            'amount' => $this->transaction['transaction_amount'],
            'type' => $classification['transaction_category'],
            'account_code' => $classification['account_code'],
            'tax_implications' => json_encode($classification['tax_implications']),
            'business_purpose' => $classification['business_purpose'],
            'source' => 'bank_import',
            'source_type' => 'bank_transaction'
        ]);
    }
}