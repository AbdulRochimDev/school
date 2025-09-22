<?php
namespace Database\Factories;

use App\Models\{LedgerEntry, Ledger};
use Illuminate\Database\Eloquent\Factories\Factory;

class LedgerEntryFactory extends Factory
{
    protected $model = LedgerEntry::class;
    public function definition(): array
    {
        $ledger = Ledger::factory()->create();
        return [
            'ledger_id' => $ledger->id,
            'entry_date' => now()->toDateString(),
            'type' => $this->faker->randomElement(['debit','credit']),
            'amount' => $this->faker->randomFloat(2, 10, 1000),
            'reference' => 'REF-'.$this->faker->numerify('####'),
            'note' => $this->faker->sentence(),
        ];
    }
}

