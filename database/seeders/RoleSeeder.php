<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Catatan: definisi role dimiliki Irul (Task 1 & 2). Seeder ini dibuat
     * sementara sebagai fondasi agar task lain dapat berjalan, dan perlu
     * dikoordinasikan dengan Irul sebelum Sprint 1 selesai.
     */
    public function run(): void
    {
        foreach (['wali_murid', 'guru', 'guru_piket', 'admin'] as $role) {
            Role::firstOrCreate([
                'name' => $role,
                'guard_name' => 'web',
            ]);
        }
    }
}
