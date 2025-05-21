<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Kegiatan>
 */
class KegiatanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_kegiatan' => $this->faker->sentence(3),
            'slug' => $this->faker->slug(),
            'tanggal_mulai' => $this->faker->date(),
            'tanggal_selesai' => $this->faker->date(),
            'beban_anggaran' => $this->faker->randomNumber(5),
            'fungsi_id' => $this->faker->randomNumber(),
            'tim_kerja_id' => $this->faker->numberBetween(1, 5),
            'honor_nias' => $this->faker->randomNumber(5),
            'honor_nias_barat' => $this->faker->randomNumber(5),
        ];
    }
}
