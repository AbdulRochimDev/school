<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RolesTableSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name'=>'Super Admin','slug'=>'super_admin'],
            ['name'=>'Admin','slug'=>'admin'],
            ['name'=>'Admin Akademik','slug'=>'admin_akademik'],
            ['name'=>'Admin Keuangan','slug'=>'admin_keuangan'],
            ['name'=>'Operator PPDB','slug'=>'operator_ppdb'],
            ['name'=>'Guru','slug'=>'guru'],
            ['name'=>'Wali Kelas','slug'=>'wali_kelas'],
            ['name'=>'Siswa','slug'=>'siswa'],
        ];
        foreach ($roles as $r) {
            Role::firstOrCreate(['slug'=>$r['slug']], $r);
        }
    }
}

