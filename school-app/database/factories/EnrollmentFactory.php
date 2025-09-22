<?php
namespace Database\Factories;

use App\Models\{Enrollment, SchoolClass, Student, Term};
use Illuminate\Database\Eloquent\Factories\Factory;

class EnrollmentFactory extends Factory
{
    protected $model = Enrollment::class;
    public function definition(): array
    {
        $class = SchoolClass::factory()->create();
        $student = Student::factory()->create(['class_id' => $class->id]);
        $term = Term::factory()->create();
        return [
            'class_id' => $class->id,
            'student_id' => $student->id,
            'term_id' => $term->id,
            'enrolled_at' => now(),
        ];
    }
}

