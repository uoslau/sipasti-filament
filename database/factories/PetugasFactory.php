<?php

namespace Database\Factories;

use App\Models\Kegiatan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Petugas>
 */
class PetugasFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'mitra_id' => $this->faker->numberBetween(1, 100),
            'kegiatan_id' => $this->faker->numberBetween(1, 5),
            'bertugas_sebagai' => $this->faker->randomElement(['PCL', 'PML']),
            'wilayah_tugas' => $this->faker->randomElement(['1201', '1225']),
            'beban' => $this->faker->numberBetween(1, 15),
            'satuan' => $this->faker->randomElement(['Dokumen', 'BS', 'Rumah Tangga']),
            'honor' => 0,
            'no_kontrak' => $this->faker->unique()->randomNumber(5, true),
            'no_bast' => $this->faker->unique()->randomNumber(5, true),
        ];
    }
}
