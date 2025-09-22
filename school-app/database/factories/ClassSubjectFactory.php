<?php
namespace Database\Factories;

use App\Models\{ClassSubject, SchoolClass, Subject, Teacher};
use Illuminate\Database\Eloquent\Factories\Factory;

class ClassSubjectFactory extends Factory
{
    protected $model = ClassSubject::class;
    public function definition(): array
    {
        $class = SchoolClass::factory()->create();
        $subject = Subject::factory()->create();
        $teacher = Teacher::factory()->create();
        return [
            'class_id' => $class->id,
            'subject_id' => $subject->id,
            'teacher_id' => $teacher->id,
        ];
    }
}

