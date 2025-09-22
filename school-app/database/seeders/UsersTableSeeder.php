<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\{User, Teacher, Student};

class UsersTableSeeder extends Seeder
{
    public function run(): void
    {
        // Super Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            ['name' => 'Super Admin', 'password' => Hash::make('password')]
        );
        $roleId = DB::table('roles')->where('slug','super_admin')->value('id');
        if ($roleId) DB::table('role_user')->insertOrIgnore(['role_id'=>$roleId,'user_id'=>$admin->id]);

        // Guru
        $teacherUser = User::firstOrCreate(
            ['email' => 'teacher@example.com'],
            ['name' => 'Guru Contoh', 'password' => Hash::make('password')]
        );
        $roleGuru = DB::table('roles')->where('slug','guru')->value('id');
        if ($roleGuru) DB::table('role_user')->insertOrIgnore(['role_id'=>$roleGuru,'user_id'=>$teacherUser->id]);
        Teacher::firstOrCreate(['user_id'=>$teacherUser->id], ['name'=>'Guru Contoh','nip'=>'100001']);

        // Siswa
        $studentUser = User::firstOrCreate(
            ['email' => 'student@example.com'],
            ['name' => 'Siswa Contoh', 'password' => Hash::make('password')]
        );
        $roleSiswa = DB::table('roles')->where('slug','siswa')->value('id');
        if ($roleSiswa) DB::table('role_user')->insertOrIgnore(['role_id'=>$roleSiswa,'user_id'=>$studentUser->id]);
        Student::firstOrCreate(['user_id'=>$studentUser->id], ['name'=>'Siswa Contoh']);
    }
}

