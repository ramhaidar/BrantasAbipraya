<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AlatFactory extends Factory
{
    public function definition () : array
    {
        return [ 
            'nomor'      => $this->faker->numerify ( '####' ),
            'jenis_alat' => $this->faker->word,
            'tipe_alat'  => $this->faker->word,
            'kode_alat'  => $this->faker->word,
        ];
    }
}