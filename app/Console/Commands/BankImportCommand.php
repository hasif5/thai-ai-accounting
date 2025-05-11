<?php

namespace App\Console\Commands;

use App\Jobs\ClassifyBankTxn;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class BankImportCommand extends Command
{
    protected $signature = 'bank:import';
    protected $description = 'Import bank transactions from BOT Open API';

    public function handle(): void
    {
        $this->info('Fetching bank transactions...');

        // Mock BOT API response with sample transactions
        $mockTransactions = [
            [
                'transaction_date' => '2024-01-15',
                'transaction_description' => 'TRANSFER FROM SOMCHAI CO.,LTD',
                'transaction_amount' => 50000.00,
                'transaction_type' => 'credit'
            ],
            [
                'transaction_date' => '2024-01-16',
                'transaction_description' => 'OFFICE RENT PAYMENT',
                'transaction_amount' => 25000.00,
                'transaction_type' => 'debit'
            ]
        ];

        $count = 0;
        foreach ($mockTransactions as $transaction) {
            ClassifyBankTxn::dispatch($transaction);
            $count++;
        }

        $this->info("Dispatched {$count} transactions for classification.");
    }
}