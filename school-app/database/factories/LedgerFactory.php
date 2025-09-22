<?php
namespace Database\Factories;

use App\Models\Ledger;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class LedgerFactory extends Factory
{
    protected $model = Ledger::class;
    public function definition(): array
    {
        $name = $this->faker->randomElement(['Tuition','Donations','Misc']);
        return [
            'name' => $name,
            'code' => Str::slug($name).'-'.$this->faker->randomNumber(3),
        ];
    }
}

