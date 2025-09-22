<?php
namespace Database\Factories;

use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TeacherFactory extends Factory
{
    protected $model = Teacher::class;
    public function definition(): array
    {
        $user = User::factory()->create([
            'password' => Hash::make('password')
        ]);
        // attach role guru if exists
        try {
            $roleId = DB::table('roles')->where('slug','guru')->value('id');
            if ($roleId) DB::table('role_user')->insertOrIgnore(['role_id'=>$roleId,'user_id'=>$user->id]);
        } catch (\Throwable $e) {}
        return [
            'user_id' => $user->id,
            'nip' => (string)$this->faker->numberBetween(100000,999999),
            'name' => $this->faker->name(),
        ];
    }
}

