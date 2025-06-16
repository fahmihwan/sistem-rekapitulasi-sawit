<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Penjualan>
 */

use Illuminate\Support\Str;

class PenjualanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // 'id' => (string) Str::uuid(),
            'sopir_id'        =>  3,
            'do_type_id'      =>  $this->faker->numberBetween(1, 3),
            'timbangan_first'  => $this->faker->numberBetween(1000, 5000),
            'timbangan_second' => $this->faker->numberBetween(1000, 5000),
            'bruto'            => $this->faker->randomFloat(2, 1000, 5000),
            'sortasi'          => $this->faker->randomFloat(2, 10, 100),
            'netto'            => $this->faker->randomFloat(2, 1000, 4900),
            'harga'            => $this->faker->numberBetween(5000, 15000),
            'uang'             => $this->faker->numberBetween(1000000, 5000000),
        ];
    }
}
