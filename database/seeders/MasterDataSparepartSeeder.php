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
            'nama'                  => 'Filter Oli C-1007',
            'part_number'           => 'C-1007',
            'merk'                  => 'Sakura',
            'id_kategori_sparepart' => KategoriSparepart::where ( 'kode', 'B11' )->value ( 'id' ),
        ] );

        MasterDataSparepart::create ( [ 
            'nama'                  => 'Klakson Keong 24V',
            'part_number'           => '24V-Klakson',
            'merk'                  => 'Bosch',
            'id_kategori_sparepart' => KategoriSparepart::where ( 'kode', 'C1' )->value ( 'id' ),
        ] );

        MasterDataSparepart::create ( [ 
            'nama'                  => 'Kipas Angin Dashboard Exca',
            'part_number'           => 'Exca-Kipas-01',
            'merk'                  => 'Exca',
            'id_kategori_sparepart' => KategoriSparepart::where ( 'kode', 'C1' )->value ( 'id' ),
        ] );

        MasterDataSparepart::create ( [ 
            'nama'                  => 'Grease Top 1',
            'part_number'           => 'Top-1-Grease',
            'merk'                  => 'Top 1',
            'id_kategori_sparepart' => KategoriSparepart::where ( 'kode', 'B27' )->value ( 'id' ),
        ] );

        MasterDataSparepart::create ( [ 
            'nama'                  => 'Kampas Rem Depan',
            'part_number'           => 'Honda',
            'merk'                  => 'TRW',
            'id_kategori_sparepart' => KategoriSparepart::where ( 'kode', 'A9' )->value ( 'id' ),
        ] );

        // =======================================

        // Menambahkan 5 data dengan merk yang berbeda
        MasterDataSparepart::create ( [ 
            'nama'                  => 'Filter Oli C-1007',
            'part_number'           => 'C-1007',
            'merk'                  => 'Denso',
            'id_kategori_sparepart' => KategoriSparepart::where ( 'kode', 'B11' )->value ( 'id' ),
        ] );

        MasterDataSparepart::create ( [ 
            'nama'                  => 'Klakson Keong 24V',
            'part_number'           => '24V-Klakson',
            'merk'                  => 'Hella',
            'id_kategori_sparepart' => KategoriSparepart::where ( 'kode', 'C1' )->value ( 'id' ),
        ] );

        MasterDataSparepart::create ( [ 
            'nama'                  => 'Kipas Angin Dashboard Exca',
            'part_number'           => 'Exca-Kipas-01',
            'merk'                  => 'Panasonic',
            'id_kategori_sparepart' => KategoriSparepart::where ( 'kode', 'C1' )->value ( 'id' ),
        ] );

        MasterDataSparepart::create ( [ 
            'nama'                  => 'Grease Top 1',
            'part_number'           => 'Top-1-Grease',
            'merk'                  => 'Shell',
            'id_kategori_sparepart' => KategoriSparepart::where ( 'kode', 'B27' )->value ( 'id' ),
        ] );

        MasterDataSparepart::create ( [ 
            'nama'                  => 'Kampas Rem Depan',
            'part_number'           => 'Honda',
            'merk'                  => 'Brembo',
            'id_kategori_sparepart' => KategoriSparepart::where ( 'kode', 'A9' )->value ( 'id' ),
        ] );

        // Additional 25 spareparts
        $spareparts = [ 
            [ 'nama' => 'Oil Filter Element', 'part_number' => 'OF-2345', 'merk' => 'Toyota', 'kode' => 'B11' ],
            [ 'nama' => 'Air Filter', 'part_number' => 'AF-1122', 'merk' => 'Mann', 'kode' => 'B11' ],
            [ 'nama' => 'Fuel Filter', 'part_number' => 'FF-3344', 'merk' => 'Bosch', 'kode' => 'B11' ],
            [ 'nama' => 'Brake Pad Set', 'part_number' => 'BP-5566', 'merk' => 'Akebono', 'kode' => 'A9' ],
            [ 'nama' => 'Timing Belt', 'part_number' => 'TB-7788', 'merk' => 'Gates', 'kode' => 'B27' ],
            [ 'nama' => 'Spark Plug', 'part_number' => 'SP-9900', 'merk' => 'NGK', 'kode' => 'C1' ],
            [ 'nama' => 'Water Pump', 'part_number' => 'WP-1234', 'merk' => 'Aisin', 'kode' => 'B27' ],
            [ 'nama' => 'Alternator', 'part_number' => 'AL-5678', 'merk' => 'Denso', 'kode' => 'C1' ],
            [ 'nama' => 'Starter Motor', 'part_number' => 'SM-9012', 'merk' => 'Bosch', 'kode' => 'C1' ],
            [ 'nama' => 'Radiator', 'part_number' => 'RD-3456', 'merk' => 'Koyorad', 'kode' => 'B27' ],
            [ 'nama' => 'Shock Absorber', 'part_number' => 'SA-7890', 'merk' => 'KYB', 'kode' => 'A9' ],
            [ 'nama' => 'CV Joint', 'part_number' => 'CV-2345', 'merk' => 'NTN', 'kode' => 'A9' ],
            [ 'nama' => 'Ball Joint', 'part_number' => 'BJ-6789', 'merk' => 'Moog', 'kode' => 'A9' ],
            [ 'nama' => 'Tie Rod End', 'part_number' => 'TR-0123', 'merk' => 'CTR', 'kode' => 'A9' ],
            [ 'nama' => 'Wheel Bearing', 'part_number' => 'WB-4567', 'merk' => 'SKF', 'kode' => 'A9' ],
            [ 'nama' => 'Engine Mount', 'part_number' => 'EM-8901', 'merk' => 'Hutchinson', 'kode' => 'B27' ],
            [ 'nama' => 'Clutch Kit', 'part_number' => 'CK-2345', 'merk' => 'Exedy', 'kode' => 'B27' ],
            [ 'nama' => 'Battery', 'part_number' => 'BT-6789', 'merk' => 'GS Yuasa', 'kode' => 'C1' ],
            [ 'nama' => 'Wiper Blade', 'part_number' => 'WB-0123', 'merk' => 'Denso', 'kode' => 'C1' ],
            [ 'nama' => 'Thermostat', 'part_number' => 'TS-4567', 'merk' => 'Gates', 'kode' => 'B27' ],
            [ 'nama' => 'Fan Belt', 'part_number' => 'FB-8901', 'merk' => 'Mitsuboshi', 'kode' => 'B27' ],
            [ 'nama' => 'Power Steering Pump', 'part_number' => 'PS-2345', 'merk' => 'Maval', 'kode' => 'C1' ],
            [ 'nama' => 'Fuel Pump', 'part_number' => 'FP-6789', 'merk' => 'Airtex', 'kode' => 'B11' ],
            [ 'nama' => 'Oxygen Sensor', 'part_number' => 'OS-0123', 'merk' => 'Denso', 'kode' => 'C1' ],
            [ 'nama' => 'Timing Chain Kit', 'part_number' => 'TC-4567', 'merk' => 'Iwis', 'kode' => 'B27' ],
        ];

        foreach ( $spareparts as $part )
        {
            MasterDataSparepart::create ( [ 
                'nama'                  => $part[ 'nama' ],
                'part_number'           => $part[ 'part_number' ],
                'merk'                  => $part[ 'merk' ],
                'id_kategori_sparepart' => KategoriSparepart::where ( 'kode', $part[ 'kode' ] )->value ( 'id' ),
            ] );
        }
    }
}
