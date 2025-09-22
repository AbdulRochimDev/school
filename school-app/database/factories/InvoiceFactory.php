<?php
namespace Database\Factories;

use App\Models\{Invoice, Student};
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;
    public function definition(): array
    {
        $student = Student::factory()->create();
        return [
            'student_id' => $student->id,
            'number' => 'INV-'.$this->faker->unique()->numerify('#######'),
            'amount' => $this->faker->randomFloat(2, 50, 500),
            'status' => 'pending',
            'due_date' => now()->addDays(14),
            'issued_at' => now(),
        ];
    }
}

