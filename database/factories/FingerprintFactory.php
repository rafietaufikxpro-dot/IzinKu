<?php

namespace Database\Factories;

use App\Models\Fingerprint;
use App\Models\Siswa;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Fingerprint>
 */
class FingerprintFactory extends Factory
{
    protected $model = Fingerprint::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'siswa_id' => Siswa::factory(),
            'tanggal' => today()->toDateString(),
            'waktu' => now()->format('H:i:s'),
        ];
    }
}
