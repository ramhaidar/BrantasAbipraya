<?php


namespace Database\Factories;

use App\Models\Alat;
use Faker\Factory as FakerFactory;
use Illuminate\Database\Eloquent\Factories\Factory;

class APBFactory extends Factory
{
    public function definition () : array
    {
        $faker = FakerFactory::create ( 'id_ID' );

        return [ 
            'id_master_data_alat' => Alat::inRandomOrder ()->first ()->id,
            'quantity'            => null,
            'tanggal'             => $faker->date ( 'Y-m-d' ),
            'id_saldo'            => null,
        ];
    }
}
