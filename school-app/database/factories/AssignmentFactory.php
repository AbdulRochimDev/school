<?php
namespace Database\Factories;

use App\Models\{Assignment, ClassSubject};
use Illuminate\Database\Eloquent\Factories\Factory;

class AssignmentFactory extends Factory
{
    protected $model = Assignment::class;
    public function definition(): array
    {
        $cs = ClassSubject::factory()->create();
        return [
            'class_subject_id' => $cs->id,
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'due_at' => now()->addWeek(),
            'max_score' => 100,
        ];
    }
}

