<?php
namespace Database\Factories;

use App\Models\{Student, User, SchoolClass};
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class StudentFactory extends Factory
{
    protected $model = Student::class;
    public function definition(): array
    {
        $user = User::factory()->create([
            'password' => Hash::make('password'),
        ]);
        try {
            $roleId = DB::table('roles')->where('slug','siswa')->value('id');
            if ($roleId) DB::table('role_user')->insertOrIgnore(['role_id'=>$roleId,'user_id'=>$user->id]);
        } catch (\Throwable $e) {}
        $class = SchoolClass::factory()->create();
        return [
            'user_id' => $user->id,
            'class_id' => $class->id,
            'nis' => (string)$this->faker->unique()->numberBetween(10000,99999),
            'nisn' => (string)$this->faker->unique()->numberBetween(100000,999999),
            'name' => $this->faker->name(),
        ];
    }
}

