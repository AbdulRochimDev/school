<?php
namespace App\Services;

use App\Models\{Ledger, LedgerEntry, Payment};

class FinancePostingService
{
    public function postPayment(Payment $payment): LedgerEntry
    {
        $ledger = Ledger::firstOrCreate(['code' => 'tuition'], ['name' => 'Tuition']);
        $ref = 'PAY#'.$payment->id;
        $existing = LedgerEntry::where('reference', $ref)->first();
        if ($existing) return $existing;
        return LedgerEntry::create([
            'ledger_id' => $ledger->id,
            'entry_date' => now()->toDateString(),
            'type' => 'credit',
            'amount' => $payment->amount,
            'reference' => $ref,
            'note' => 'Payment verified',
        ]);
    }
}

