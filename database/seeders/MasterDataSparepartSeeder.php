<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KategoriSparepart;
use App\Models\MasterDataSparepart;

class MasterDataSparepartSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    // public function run ()
    // {
    //     MasterDataSparepart::factory ()->count ( 300 )->create ();
    // }

    public function run ()
    {
        // Menambahkan 5 data secara manual
        MasterDataSparepart::create ( [ 
            'nama'        => 'Filter Oli C-1007',
            'part_number' => 'C-1007',
            'merk'        => 'Caterpillar',
            'id_kategori' => KategoriSparepart::where ( 'kode', 'B11' )->value ( 'id' ),
        ] );

        MasterDataSparepart::create ( [ 
            'nama'        => 'Klakson Keong 24V',
            'part_number' => '24V-Klakson',
            'merk'        => 'Bosch',
            'id_kategori' => KategoriSparepart::where ( 'kode', 'C1' )->value ( 'id' ),
        ] );

        MasterDataSparepart::create ( [ 
            'nama'        => 'Kipas Angin Dashboard Exca',
            'part_number' => 'Exca-Kipas-01',
            'merk'        => 'Exca',
            'id_kategori' => KategoriSparepart::where ( 'kode', 'C1' )->value ( 'id' ),
        ] );

        MasterDataSparepart::create ( [ 
            'nama'        => 'Grease Top 1',
            'part_number' => 'Top-1-Grease',
            'merk'        => 'Top 1',
            'id_kategori' => KategoriSparepart::where ( 'kode', 'B27' )->value ( 'id' ),
        ] );

        MasterDataSparepart::create ( [ 
            'nama'        => 'Kampas Rem Depan',
            'part_number' => 'Kampas-Rem-Depan',
            'merk'        => 'TRW',
            'id_kategori' => KategoriSparepart::where ( 'kode', 'A9' )->value ( 'id' ),
        ] );
    }
}
