<?php

namespace Database\Factories;

use App\Models\Siswa;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Siswa>
 */
class SiswaFactory extends Factory
{
    protected $model = Siswa::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'nis' => fake()->unique()->numerify('#####'),
            'nisn' => fake()->unique()->numerify('##########'),
            'nama' => fake()->name(),
            'kelas' => fake()->randomElement(['X-A', 'X-B', 'XI-RPL-1', 'XI-RPL-2', 'XII-TKJ']),
            'jenis_kelamin' => fake()->randomElement(['L', 'P']),
        ];
    }
}
