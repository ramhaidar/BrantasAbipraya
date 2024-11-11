<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Proyek>
 */
class ProyekFactory extends Factory
{
    protected $model = Proyek::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition () : array
    {
        return [ 
            'nama_proyek' => $this->faker->sentence ( 3 ) // Placeholder jika dibutuhkan data acak, tapi tidak akan digunakan dalam seeding
        ];
    }
}
