<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Enums\Transactions\TransactionPaymentStatus;
use App\Models\MembershipTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CancelExpiredTransactions extends Command
{
    protected $signature = 'transactions:cancel-expired';
    protected $description = 'Cancel expired membership transactions automatically';

    public function handle()
    {
        $now = Carbon::now();
        $transactions = MembershipTransaction::where('status', TransactionPaymentStatus::WAITING->value)
            ->where('expired_at', '<', $now)
            ->get();

        foreach ($transactions as $transaction) {
            $transaction->status = TransactionPaymentStatus::FAILED->value;
            $transaction->save();

            $this->info("Transaction {$transaction->id} expired and marked as FAILED.");
            $this->info("Transaction {$transaction->id} expired and marked as FAILED.");
            Log::info("Transaction {$transaction->id} expired and marked as FAILED.");
            Log::nfo("Transaction {$transaction->id} expired and marked as FAILED.");
        }

        return Command::SUCCESS;
    }
}
