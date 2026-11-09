<?php

namespace Database\Seeders;

use App\Models\Siswa;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DummyDataSeeder extends Seeder
{
    /**
     * Data dummy untuk pengembangan dan demonstrasi.
     *
     * Jangan gunakan data siswa, nomor WhatsApp, atau dokumen nyata.
     */
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin Dummy',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        );
        $admin->syncRoles('admin');

        $guru = User::firstOrCreate(
            ['email' => 'guru@example.com'],
            [
                'name' => 'Guru Dummy',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        );
        $guru->syncRoles('guru');

        $piket = User::firstOrCreate(
            ['email' => 'piket@example.com'],
            [
                'name' => 'Guru Piket Dummy',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        );
        $piket->syncRoles('guru_piket');

        $wali = User::firstOrCreate(
            ['email' => 'wali@example.com'],
            [
                'name' => 'Wali Dummy',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'nomor_whatsapp' => '081234567890',
            ],
        );
        $wali->syncRoles('wali_murid');

        Siswa::firstOrCreate(
            ['nama' => 'Siswa Dummy'],
            [
                'kelas' => 'XII RPL 1',
                'user_id' => $wali->id,
            ],
        );
    }
}
