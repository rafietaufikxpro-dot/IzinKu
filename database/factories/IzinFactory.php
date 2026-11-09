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
        $startDate = now()->addDay();
        $endDate = (clone $startDate)->addDays(1);

        return [
            'user_id' => User::factory(),
            'siswa_id' => Siswa::factory(),
            'jenis_izin' => 'izin',
            'tanggal_mulai' => $startDate->toDateString(),
            'tanggal_selesai' => $endDate->toDateString(),
            'jumlah_hari' => 2,
            'alasan' => fake()->sentence(),
            'surat_sakit' => null,
            'status_konfirmasi' => 'menunggu',
            'status' => 'menunggu',
            'catatan_penolakan' => null,
            'diverifikasi_oleh' => null,
            'diverifikasi_pada' => null,
        ];
    }

    /**
     * State untuk izin jenis sakit.
     */
    public function sakit(): static
    {
        return $this->state(fn () => [
            'jenis_izin' => 'sakit',
            'surat_sakit' => 'surat_sakit/dummy.pdf',
        ]);
    }
}
