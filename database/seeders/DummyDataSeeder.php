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
                'name' => 'Wali Murid Demo',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'nomor_whatsapp' => '081234567890',
            ],
        );
        $wali->syncRoles('wali_murid');

        // Data siswa gabungan (demo Tristan & Rapik).
        $ahmad = Siswa::firstOrCreate(
            ['nis' => '10291'],
            [
                'user_id' => $wali->id,
                'nisn' => '0081234567',
                'nama' => 'Ahmad Fauzi',
                'kelas' => 'XI-RPL-1',
                'jenis_kelamin' => 'L',
            ],
        );

        Siswa::firstOrCreate(
            ['nis' => '10292'],
            [
                'user_id' => $wali->id,
                'nisn' => '0081234568',
                'nama' => 'Siti Nurhaliza',
                'kelas' => 'X-A',
                'jenis_kelamin' => 'P',
            ],
        );

        // Demo Task 7: izin menunggu yang sudah dikonfirmasi.
        Izin::firstOrCreate(
            ['siswa_id' => $ahmad->id, 'jenis_izin' => 'izin', 'tanggal_mulai' => now()->toDateString()],
            [
                'user_id' => $wali->id,
                'tanggal_selesai' => now()->toDateString(),
                'jumlah_hari' => 1,
                'alasan' => 'Izin dummy untuk demo verifikasi.',
                'status_konfirmasi' => 'dikonfirmasi',
                'dikonfirmasi_oleh' => $wali->id,
                'dikonfirmasi_pada' => now(),
                'status' => 'menunggu',
            ],
        );

        // Data dummy untuk deteksi kehadiran (Task 8).
        $siswaHadir = Siswa::firstOrCreate(
            ['nis' => '10293'],
            [
                'user_id' => $wali->id,
                'nama' => 'Siswa Dummy Hadir',
                'kelas' => 'XII RPL 1',
                'jenis_kelamin' => 'L',
            ],
        );
        Fingerprint::firstOrCreate(
            ['siswa_id' => $siswaHadir->id, 'tanggal' => today()->toDateString()],
            ['waktu' => '06:45:00'],
        );

        Siswa::firstOrCreate(
            ['nis' => '10294'],
            [
                'user_id' => $wali->id,
                'nama' => 'Siswa Dummy Tidak Hadir',
                'kelas' => 'XII RPL 1',
                'jenis_kelamin' => 'P',
            ],
        );

        $siswaIzin = Siswa::firstOrCreate(
            ['nis' => '10295'],
            [
                'user_id' => $wali->id,
                'nama' => 'Siswa Dummy Izin Disetujui',
                'kelas' => 'XII RPL 1',
                'jenis_kelamin' => 'L',
            ],
        );
        Izin::firstOrCreate(
            ['siswa_id' => $siswaIzin->id, 'jenis_izin' => 'izin', 'tanggal_mulai' => now()->toDateString()],
            [
                'user_id' => $wali->id,
                'tanggal_selesai' => now()->toDateString(),
                'jumlah_hari' => 1,
                'alasan' => 'Izin dummy disetujui untuk demo deteksi.',
                'status_konfirmasi' => 'dikonfirmasi',
                'dikonfirmasi_oleh' => $wali->id,
                'dikonfirmasi_pada' => now(),
                'status' => 'disetujui',
                'diverifikasi_oleh' => $piket->id,
                'diverifikasi_pada' => now(),
            ],
        );
    }
}
