<?php

namespace Database\Factories;

use App\Models\Izin;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Izin>
 */
class IzinFactory extends Factory
{
    protected $model = Izin::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'siswa_id' => Siswa::factory(),
            'user_id' => User::factory(),
            'alasan' => 'sakit',
            'tanggal_mulai' => now()->toDateString(),
            'tanggal_selesai' => now()->toDateString(),
            'surat_sakit_path' => null,
            'status' => 'menunggu',
            'alasan_penolakan' => null,
            'catatan' => null,
            'confirmed_at' => null,
            'confirmed_by' => null,
            'status_updated_at' => null,
            'status_updated_by' => null,
        ];
    }
}
