<?php
namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class RoleFactory extends Factory
{
    protected $model = Role::class;
    public function definition(): array
    {
        $name = $this->faker->unique()->jobTitle();
        return [
            'name' => $name,
            'slug' => Str::slug($name.' '.Str::random(4)),
        ];
    }
}

