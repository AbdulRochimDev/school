<?php
namespace Database\Factories;

use App\Models\SchoolClass;
use Illuminate\Database\Eloquent\Factories\Factory;

class SchoolClassFactory extends Factory
{
    protected $model = SchoolClass::class;
    public function definition(): array
    {
        return [
            'name' => 'Class '.$this->faker->randomElement(['A','B','C']).' '.$this->faker->numberBetween(1,12),
            'level' => (string)$this->faker->numberBetween(1,12),
            'homeroom_teacher_id' => null,
        ];
    }
}

