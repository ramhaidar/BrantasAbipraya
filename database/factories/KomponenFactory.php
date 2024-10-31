<?php
namespace Database\Factories;

use App\Models\FirstGroup;
use App\Models\SecondGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

class KomponenFactory extends Factory
{
    public function definition () : array
    {
        $kodeA        = array_map ( fn ( $number ) => 'A' . $number, range ( 1, 14 ) );
        $kodeB        = array_merge ( array_map ( fn ( $number ) => 'B' . $number, range ( 11, 16 ) ), array_map ( fn ( $number ) => 'B' . $number, range ( 21, 29 ) ) );
        $kodeB[]      = 'B3';
        $kodeC        = [ 'C1' ];
        $kodeOptions  = array_merge ( $kodeA, $kodeB, $kodeC );
        $kode         = $this->faker->randomElement ( $kodeOptions );
        $first_group  = null;
        $second_group = null;
        if ( preg_match ( '/^A(1[0-4]?|[1-9])$/', $kode ) )
        {
            $first_group = FirstGroup::factory ()->state ( [ 'name' => 'PERBAIKAN' ] );
        }
        elseif ( $kode === 'B3' )
        {
            $first_group = FirstGroup::factory ()->state ( [ 'name' => 'PEMELIHARAAN' ] );
        }
        elseif ( $kode === 'C1' )
        {
            $first_group = FirstGroup::factory ()->state ( [ 'name' => 'WORKSHOP' ] );
        }
        elseif ( $kode >= 'B11' && $kode <= 'B29' )
        {
            $first_group = FirstGroup::factory ()->state ( [ 'name' => 'PEMELIHARAAN' ] );
            if ( $kode >= 'B11' && $kode <= 'B16' )
            {
                $second_group = SecondGroup::factory ()->state ( [ 'name' => 'MAINTENANCE KIT' ] );
            }
            elseif ( $kode >= 'B21' && $kode <= 'B29' )
            {
                $second_group = SecondGroup::factory ()->state ( [ 'name' => 'OIL & LUBRICANTS' ] );
            }
        }
        return [ 
            'nama_proyek'     => null,
            'kode'            => $kode,
            'first_group_id'  => $first_group ? $first_group : null,
            'second_group_id' => $second_group ? $second_group : null,
        ];
    }

    public function configureForAll ( $net )
    {
        return $this->state ( fn ( array $attributes ) => [ 'nama_proyek' => $this->faker->company ] );
    }

    public function configureForMutasiProyek ( $net )
    {
        return $this->state ( fn ( array $attributes ) => [ 'asal_proyek' => $this->faker->company, 'nama_proyek' => $this->faker->company ] );
    }

    // public function configureForATB ( $net )
    // {
    //     return $this->state ( fn ( array $attributes ) => [ 'name_for_atb' => $this->faker->company, 'name_for_apb' => null, 'value' => $net,] );
    // }
    // public function configureForAPB ( $net )
    // {
    //     return $this->state ( fn ( array $attributes ) => [ 'name_for_atb' => null, 'name_for_apb' => $this->faker->company, 'value' => $net,] );
    // }
}