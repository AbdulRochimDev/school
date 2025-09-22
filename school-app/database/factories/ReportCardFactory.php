<?php
namespace Database\Factories;

use App\Models\{ReportCard, Student, Term};
use Illuminate\Database\Eloquent\Factories\Factory;

class ReportCardFactory extends Factory
{
    protected $model = ReportCard::class;
    public function definition(): array
    {
        $student = Student::factory()->create();
        $term = Term::factory()->create();
        return [
            'student_id' => $student->id,
            'term_id' => $term->id,
            'final_score' => $this->faker->numberBetween(60, 100),
            'published_at' => now(),
        ];
    }
}

