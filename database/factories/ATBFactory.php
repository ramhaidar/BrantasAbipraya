<?php

namespace Database\Factories;

use App\Models\Komponen;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Factory as FakerFactory;

class ATBFactory extends Factory
{
    public function definition () : array
    {
        $faker = FakerFactory::create ( 'id_ID' );

        $harga    = $faker->numberBetween ( 100, 999 ) * 1000;
        $quantity = $faker->numberBetween ( 1, 30 );
        $net      = $quantity * $harga;
        $ppn      = $net * 0.11;
        $bruto    = $net + $ppn;
        $tipe     = [ 'Hutang Unit Alat', 'Panjar Unit Alat', 'Mutasi Proyek', 'Panjar Proyek' ];

        $tipeChoice = $faker->randomElement ( $tipe );
        if ( $tipeChoice == "Mutasi Proyek" )
        {
            return [ 
                'tipe'        => $tipeChoice,
                'tanggal'     => $faker->date ( 'Y-m-d' ),
                'supplier'    => $faker->company,
                'sparepart'   => $faker->word,
                'part_number' => $faker->bothify ( '???-####' ),
                'quantity'    => $quantity,
                'satuan'      => $faker->randomElement ( [ 'PCS', 'SET', 'BTL', 'LTR' ] ),
                'harga'       => $harga,
                'net'         => $net,
                'ppn'         => $ppn,
                'bruto'       => $bruto,
                'id_komponen' => Komponen::factory ()->configureForMutasiProyek ( $net ),
            ];
        }
        else
        {
            return [ 
                'tipe'        => $tipeChoice,
                'tanggal'     => $faker->date ( 'Y-m-d' ),
                'supplier'    => $faker->company,
                'sparepart'   => $faker->word,
                'part_number' => $faker->bothify ( '???-####' ),
                'quantity'    => $quantity,
                'satuan'      => $faker->randomElement ( [ 'PCS', 'SET', 'BTL', 'LTR' ] ),
                'harga'       => $harga,
                'net'         => $net,
                'ppn'         => $ppn,
                'bruto'       => $bruto,
                'id_komponen' => Komponen::factory ()->configureForAll ( $net ),
            ];
        }
    }
}
