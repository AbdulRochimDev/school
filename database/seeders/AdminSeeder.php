<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        if (!class_exists(\App\Models\User::class)) return;
        $user = \App\Models\User::firstOrCreate(
            ['email' => 'admin@example.com'],
            ['name' => 'Admin', 'password' => Hash::make('password')]
        );
        // attach role via pivot if available
        try {
            if (method_exists($user, 'roles')) {
                $role = \App\Models\Role::where('slug','super_admin')->first();
                if ($role) $user->roles()->syncWithoutDetaching([$role->id]);
            } elseif (property_exists($user, 'role')) {
                $user->role = 'super_admin';
                $user->save();
            }
        } catch (\Throwable $e) {
            // ignore
        }
    }
}

