<?php
namespace Database\Factories;

use App\Models\{PaymentVerification, Payment};
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentVerificationFactory extends Factory
{
    protected $model = PaymentVerification::class;
    public function definition(): array
    {
        $payment = Payment::factory()->create();
        return [
            'payment_id' => $payment->id,
            'verified_by' => null,
            'verified_at' => now(),
            'status' => 'verified',
            'note' => null,
        ];
    }
}

