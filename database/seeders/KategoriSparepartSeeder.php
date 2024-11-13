<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KategoriSparepart;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class KategoriSparepartSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run ()
    {
        $data = [ 
            [ 'kode' => 'A1', 'nama' => 'CABIN' ],
            [ 'kode' => 'A2', 'nama' => 'ENGINE SYSTEM' ],
            [ 'kode' => 'A3', 'nama' => 'TRANSMISSION SYSTEM' ],
            [ 'kode' => 'A4', 'nama' => 'CHASSIS & SWING MACHINERY' ],
            [ 'kode' => 'A5', 'nama' => 'DIFFERENTIAL SYSTEM' ],
            [ 'kode' => 'A6', 'nama' => 'ELECTRICAL SYSTEM' ],
            [ 'kode' => 'A7', 'nama' => 'HYDRAULIC/PNEUMATIC SYSTEM' ],
            [ 'kode' => 'A8', 'nama' => 'STEERING SYSTEM' ],
            [ 'kode' => 'A9', 'nama' => 'BRAKE SYSTEM' ],
            [ 'kode' => 'A10', 'nama' => 'SUSPENSION' ],
            [ 'kode' => 'A11', 'nama' => 'WORK EQUIPMENT' ],
            [ 'kode' => 'A12', 'nama' => 'UNDERCARRIAGE' ],
            [ 'kode' => 'A13', 'nama' => 'FINAL DRIVE' ],
            [ 'kode' => 'A14', 'nama' => 'FREIGHT COST' ],
            [ 'kode' => 'B11', 'nama' => 'Oil Filter' ],
            [ 'kode' => 'B12', 'nama' => 'Fuel Filter' ],
            [ 'kode' => 'B13', 'nama' => 'Air Filter' ],
            [ 'kode' => 'B14', 'nama' => 'Hydraulic Filter' ],
            [ 'kode' => 'B15', 'nama' => 'Transmission Filter' ],
            [ 'kode' => 'B16', 'nama' => 'Differential Filter' ],
            [ 'kode' => 'B21', 'nama' => 'Engine Oil' ],
            [ 'kode' => 'B22', 'nama' => 'Hydraulic Oil' ],
            [ 'kode' => 'B23', 'nama' => 'Transmission Oil' ],
            [ 'kode' => 'B24', 'nama' => 'Final Drive Oil' ],
            [ 'kode' => 'B25', 'nama' => 'Swing & Damper Oil' ],
            [ 'kode' => 'B26', 'nama' => 'Differential Oil' ],
            [ 'kode' => 'B27', 'nama' => 'Grease' ],
            [ 'kode' => 'B28', 'nama' => 'Brake & Power Steering Fluid' ],
            [ 'kode' => 'B29', 'nama' => 'Coolant' ],
            [ 'kode' => 'B3', 'nama' => 'TYRE' ],
            [ 'kode' => 'C1', 'nama' => 'WORKSHOP' ],
        ];

        foreach ( $data as $item )
        {
            // Determine Jenis and SubJenis based on kode
            $jenis    = null;
            $subJenis = null;

            if ( str_starts_with ( $item[ 'kode' ], 'A' ) )
            {
                $jenis = 'Perbaikan';
            }
            elseif ( $item[ 'kode' ] === 'B3' )
            {
                $jenis = 'Pemeliharaan';
            }
            elseif ( $item[ 'kode' ] === 'C1' )
            {
                $jenis = 'Workshop';
            }
            elseif ( in_array ( $item[ 'kode' ], [ 'B11', 'B12', 'B13', 'B14', 'B15', 'B16' ] ) )
            {
                $jenis    = 'Pemeliharaan';
                $subJenis = 'MAINTENANCE KIT';
            }
            elseif ( in_array ( $item[ 'kode' ], [ 'B21', 'B22', 'B23', 'B24', 'B25', 'B26', 'B27', 'B28', 'B29' ] ) )
            {
                $jenis    = 'Pemeliharaan';
                $subJenis = 'OIL & LUBRICANTS';
            }

            // Add jenis and sub_jenis to the item array
            $item[ 'jenis' ]     = $jenis;
            $item[ 'sub_jenis' ] = $subJenis;

            // Create the record with overridden values
            KategoriSparepart::factory ()->create ( $item );
        }
    }
}
