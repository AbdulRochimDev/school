<?php
namespace Database\Factories;

use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class SubjectFactory extends Factory
{
    protected $model = Subject::class;
    public function definition(): array
    {
        $name = $this->faker->unique()->randomElement(['Math','Science','History','Geography','English','Biology']);
        return [
            'name' => $name,
            'code' => Str::upper(Str::substr($name,0,3)).$this->faker->numberBetween(100,999),
        ];
    }
}

