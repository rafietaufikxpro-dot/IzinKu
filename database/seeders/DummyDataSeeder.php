<?php

namespace Database\Seeders;

use App\Models\Fingerprint;
use App\Models\Izin;
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

        $siswa = Siswa::firstOrCreate(
            ['nama' => 'Siswa Dummy'],
            [
                'kelas' => 'XII RPL 1',
                'user_id' => $wali->id,
            ],
        );

        Izin::firstOrCreate(
            [
                'siswa_id' => $siswa->id,
                'alasan' => 'izin',
                'tanggal_mulai' => now()->toDateString(),
            ],
            [
                'user_id' => $wali->id,
                'tanggal_selesai' => now()->toDateString(),
                'status' => 'menunggu',
                'catatan' => 'Izin dummy untuk demo verifikasi.',
                'confirmed_at' => now(),
                'confirmed_by' => $wali->id,
            ],
        );

        // Data dummy untuk deteksi kehadiran (Task 8).
        $siswaHadir = Siswa::firstOrCreate(
            ['nama' => 'Siswa Dummy Hadir'],
            ['kelas' => 'XII RPL 1', 'user_id' => $wali->id],
        );
        Fingerprint::firstOrCreate(
            ['siswa_id' => $siswaHadir->id, 'tanggal' => today()->toDateString()],
            ['waktu' => '06:45:00'],
        );

        Siswa::firstOrCreate(
            ['nama' => 'Siswa Dummy Tidak Hadir'],
            ['kelas' => 'XII RPL 1', 'user_id' => $wali->id],
        );

        $siswaIzin = Siswa::firstOrCreate(
            ['nama' => 'Siswa Dummy Izin Disetujui'],
            ['kelas' => 'XII RPL 1', 'user_id' => $wali->id],
        );
        Izin::firstOrCreate(
            [
                'siswa_id' => $siswaIzin->id,
                'alasan' => 'izin',
                'tanggal_mulai' => now()->toDateString(),
            ],
            [
                'user_id' => $wali->id,
                'tanggal_selesai' => now()->toDateString(),
                'status' => 'disetujui',
                'catatan' => 'Izin dummy disetujui untuk demo deteksi.',
                'confirmed_at' => now(),
                'confirmed_by' => $wali->id,
                'status_updated_at' => now(),
                'status_updated_by' => $piket->id,
            ],
        );
    }
}
