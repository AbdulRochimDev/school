<?php
namespace App\Listeners;

use App\Events\PaymentVerified;
use App\Services\FinancePostingService;

class CreateLedgerEntriesOnPaymentVerified
{
    public function handle(PaymentVerified $event): void
    {
        app(FinancePostingService::class)->postPayment($event->payment);
    }
}
