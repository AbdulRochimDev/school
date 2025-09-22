<?php
namespace Database\Factories;

use App\Models\{Grade, GradeItem, Student};
use Illuminate\Database\Eloquent\Factories\Factory;

class GradeFactory extends Factory
{
    protected $model = Grade::class;
    public function definition(): array
    {
        $gi = GradeItem::factory()->create();
        $student = Student::factory()->create();
        return [
            'grade_item_id' => $gi->id,
            'student_id' => $student->id,
            'score' => $this->faker->numberBetween(50, 100),
            'graded_at' => now(),
        ];
    }
}

