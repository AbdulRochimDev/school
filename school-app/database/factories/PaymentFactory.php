<?php
namespace Database\Factories;

use App\Models\{Payment, Invoice};
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;
    public function definition(): array
    {
        $invoice = Invoice::factory()->create();
        return [
            'invoice_id' => $invoice->id,
            'amount' => $invoice->amount,
            'method' => $this->faker->randomElement(['cash','transfer']),
            'paid_at' => now(),
            'reference' => 'PAY-'.$this->faker->numerify('####'),
        ];
    }
}

