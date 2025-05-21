<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Mitra>
 */
class MitraFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nik' => $this->faker->unique()->numerify('##########'),
            'nama_mitra' => $this->faker->name(),
            'posisi' => $this->faker->jobTitle(),
            'email' => $this->faker->email(),
            'alamat' => $this->faker->address(),
            'tanggal_lahir' => $this->faker->date(),
            'npwp' => $this->faker->unique()->numerify('###########'),
            'jenis_kelamin' => $this->faker->randomElement(['L', 'P']),
            'pekerjaan' => $this->faker->jobTitle(),
        ];
    }
}
