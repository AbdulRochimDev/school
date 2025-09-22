<?php
namespace Database\Factories;

use App\Models\Term;
use Illuminate\Database\Eloquent\Factories\Factory;

class TermFactory extends Factory
{
    protected $model = Term::class;
    public function definition(): array
    {
        return [
            'name' => 'Term '.$this->faker->unique()->randomElement(['1','2']).' '.date('Y'),
            'start_date' => now()->startOfYear(),
            'end_date' => now()->endOfYear(),
            'active' => true,
        ];
    }
}

