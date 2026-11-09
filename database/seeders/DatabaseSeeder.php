<?php

namespace Database\Seeders;

use App\Models\Siswa;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $wali = User::factory()->create([
            'name' => 'Wali Murid Demo',
            'email' => 'wali@example.com',
            'password' => bcrypt('password'),
        ]);

        Siswa::create([
            'user_id' => $wali->id,
            'nis' => '10291',
            'nisn' => '0081234567',
            'nama' => 'Ahmad Fauzi',
            'kelas' => 'XI-RPL-1',
            'jenis_kelamin' => 'L',
        ]);

        Siswa::create([
            'user_id' => $wali->id,
            'nis' => '10292',
            'nisn' => '0081234568',
            'nama' => 'Siti Nurhaliza',
            'kelas' => 'X-A',
            'jenis_kelamin' => 'P',
        ]);
    }
}
