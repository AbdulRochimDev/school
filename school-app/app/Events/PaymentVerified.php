<?php
namespace App\Events;

use App\Models\Payment;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;

class PaymentVerified
{
    use Dispatchable, InteractsWithSockets;

    public function __construct(public Payment $payment)
    {
    }
}

