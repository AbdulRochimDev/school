<?php
namespace Database\Factories;

use App\Models\{PPDBApplication, User};
use Illuminate\Database\Eloquent\Factories\Factory;

class PPDBApplicationFactory extends Factory
{
    protected $model = PPDBApplication::class;
    public function definition(): array
    {
        $user = User::factory()->create();
        return [
            'user_id' => $user->id,
            'status' => 'pending',
            'submitted_at' => now(),
        ];
    }
}

