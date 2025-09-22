<?php
namespace Database\Factories;

use App\Models\{GradeItem, ClassSubject};
use Illuminate\Database\Eloquent\Factories\Factory;

class GradeItemFactory extends Factory
{
    protected $model = GradeItem::class;
    public function definition(): array
    {
        $cs = ClassSubject::factory()->create();
        return [
            'class_subject_id' => $cs->id,
            'name' => $this->faker->randomElement(['Attendance','Assignment','Quiz','Exam']).' '.$this->faker->randomDigit(),
            'weight' => $this->faker->randomElement([5,10,15,20]),
            'max_score' => 100,
        ];
    }
}

