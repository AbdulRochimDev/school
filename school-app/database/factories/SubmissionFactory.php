<?php
namespace Database\Factories;

use App\Models\{Submission, Assignment, Student};
use Illuminate\Database\Eloquent\Factories\Factory;

class SubmissionFactory extends Factory
{
    protected $model = Submission::class;
    public function definition(): array
    {
        $assignment = Assignment::factory()->create();
        $student = Student::factory()->create();
        return [
            'assignment_id' => $assignment->id,
            'student_id' => $student->id,
            'content' => $this->faker->paragraph(),
            'submitted_at' => now(),
            'score' => null,
            'feedback' => null,
        ];
    }
}

