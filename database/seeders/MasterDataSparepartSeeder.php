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
            'part_number'           => 'SO-1007P', // Format Sakura Oil Filter
            'merk'                  => 'Sakura',
            'id_kategori_sparepart' => KategoriSparepart::where ( 'kode', 'B11' )->value ( 'id' ),
        ] );

        MasterDataSparepart::create ( [ 
            'nama'                  => 'Klakson Keong 24V',
            'part_number'           => '0 986 AH0 501', // Format Bosch part number
            'merk'                  => 'Bosch',
            'id_kategori_sparepart' => KategoriSparepart::where ( 'kode', 'C1' )->value ( 'id' ),
        ] );

        MasterDataSparepart::create ( [ 
            'nama'                  => 'Kipas Angin Dashboard Exca',
            'part_number'           => '11N6-90780', // Format Excavator part
            'merk'                  => 'Exca',
            'id_kategori_sparepart' => KategoriSparepart::where ( 'kode', 'C1' )->value ( 'id' ),
        ] );

        MasterDataSparepart::create ( [ 
            'nama'                  => 'Grease Top 1',
            'part_number'           => 'EP-2/10', // Format Grease specification
            'merk'                  => 'Top 1',
            'id_kategori_sparepart' => KategoriSparepart::where ( 'kode', 'B27' )->value ( 'id' ),
        ] );

        MasterDataSparepart::create ( [ 
            'nama'                  => 'Kampas Rem Depan',
            'part_number'           => 'GDB3309', // Format TRW brake pad
            'merk'                  => 'TRW',
            'id_kategori_sparepart' => KategoriSparepart::where ( 'kode', 'A9' )->value ( 'id' ),
        ] );

        // =======================================

        // Menambahkan 5 data dengan merk yang berbeda
        MasterDataSparepart::create ( [ 
            'nama'                  => 'Filter Oli C-1007',
            'part_number'           => '15208-65F0A', // Format Denso/OEM style
            'merk'                  => 'Denso',
            'id_kategori_sparepart' => KategoriSparepart::where ( 'kode', 'B11' )->value ( 'id' ),
        ] );

        MasterDataSparepart::create ( [ 
            'nama'                  => 'Klakson Keong 24V',
            'part_number'           => '3PA 004 811-851', // Format Hella part number
            'merk'                  => 'Hella',
            'id_kategori_sparepart' => KategoriSparepart::where ( 'kode', 'C1' )->value ( 'id' ),
        ] );

        MasterDataSparepart::create ( [ 
            'nama'                  => 'Kipas Angin Dashboard Exca',
            'part_number'           => 'FN12V-60B', // Format Panasonic industrial fan
            'merk'                  => 'Panasonic',
            'id_kategori_sparepart' => KategoriSparepart::where ( 'kode', 'C1' )->value ( 'id' ),
        ] );

        MasterDataSparepart::create ( [ 
            'nama'                  => 'Grease Top 1',
            'part_number'           => 'GD5/180', // Format Shell Grease
            'merk'                  => 'Shell',
            'id_kategori_sparepart' => KategoriSparepart::where ( 'kode', 'B27' )->value ( 'id' ),
        ] );

        MasterDataSparepart::create ( [ 
            'nama'                  => 'Kampas Rem Depan',
            'part_number'           => 'P85 012', // Format Brembo pad number
            'merk'                  => 'Brembo',
            'id_kategori_sparepart' => KategoriSparepart::where ( 'kode', 'A9' )->value ( 'id' ),
        ] );

        // Additional 25 spareparts
        $spareparts = [ 
            [ 'nama' => 'Oil Filter Assembly PC200-8', 'part_number' => '600-211-5241', 'merk' => 'Komatsu', 'kode' => 'B11' ],
            [ 'nama' => 'Air Cleaner Element HD785-7', 'part_number' => 'C-33-1399', 'merk' => 'Mann-Filter', 'kode' => 'B13' ],
            [ 'nama' => 'Fuel Filter DX225LCA', 'part_number' => 'K1030487', 'merk' => 'Doosan', 'kode' => 'B12' ],
            [ 'nama' => 'Brake Pad Set SK200-8', 'part_number' => 'KRA0440', 'merk' => 'Kobelco', 'kode' => 'A9' ],
            [ 'nama' => 'Timing Belt 6D16T', 'part_number' => 'ME240538', 'merk' => 'Mitsubishi', 'kode' => 'A2' ],
            [ 'nama' => 'Glow Plug 4D95L', 'part_number' => '894453-0180', 'merk' => 'Denso', 'kode' => 'A2' ],
            [ 'nama' => 'Water Pump PC300-8', 'part_number' => '6745-61-1210', 'merk' => 'Komatsu', 'kode' => 'A2' ],
            [ 'nama' => 'Alternator HD785-7', 'part_number' => '600-825-5550', 'merk' => 'Komatsu', 'kode' => 'A6' ],
            [ 'nama' => 'Starter Motor CAT320D', 'part_number' => '228000-7802', 'merk' => 'Denso', 'kode' => 'A6' ],
            [ 'nama' => 'Radiator Core SK200-8', 'part_number' => 'YN05P00035S004', 'merk' => 'Kobelco', 'kode' => 'A2' ],
            [ 'nama' => 'Shock Absorber HD465-7', 'part_number' => '569-33-41214', 'merk' => 'Kayaba', 'kode' => 'A10' ],
            [ 'nama' => 'CV Joint Assembly', 'part_number' => '42450-60171', 'merk' => 'Toyota', 'kode' => 'A4' ],
            [ 'nama' => 'Ball Joint HD785-7', 'part_number' => '281-70-74272', 'merk' => 'Komatsu', 'kode' => 'A4' ],
            [ 'nama' => 'Tie Rod End PC400-8', 'part_number' => '208-70-61250', 'merk' => 'Komatsu', 'kode' => 'A8' ],
            [ 'nama' => 'Wheel Bearing Kit', 'part_number' => 'VKBA5314', 'merk' => 'SKF', 'kode' => 'A4' ],
            [ 'nama' => 'Engine Mounting D85ESS-2', 'part_number' => '175-01-K1240', 'merk' => 'Komatsu', 'kode' => 'A2' ],
            [ 'nama' => 'Clutch Kit HD785-7', 'part_number' => '159-12-11103', 'merk' => 'Komatsu', 'kode' => 'A3' ],
            [ 'nama' => 'Battery N200', 'part_number' => 'NS200-MFH', 'merk' => 'GS Yuasa', 'kode' => 'A6' ],
            [ 'nama' => 'Wiper Blade PC200-8', 'part_number' => '198-Z5-H5810', 'merk' => 'Komatsu', 'kode' => 'A1' ],
            [ 'nama' => 'Thermostat 6D125', 'part_number' => '6151-61-1110', 'merk' => 'Komatsu', 'kode' => 'A2' ],
            [ 'nama' => 'V-Belt Set PC400-8', 'part_number' => '6136-62-1810', 'merk' => 'Bando', 'kode' => 'A2' ],
            [ 'nama' => 'Power Steering Pump D85ESS-2', 'part_number' => '705-12-44010', 'merk' => 'Komatsu', 'kode' => 'A8' ],
            [ 'nama' => 'Electric Fuel Pump HD785-7', 'part_number' => '6003-81-6550', 'merk' => 'Komatsu', 'kode' => 'A2' ],
            [ 'nama' => 'O2 Sensor Assembly', 'part_number' => '89467-60080', 'merk' => 'Denso', 'kode' => 'A2' ],
            [ 'nama' => 'Timing Chain Kit 6D140', 'part_number' => '6212-K1-9901', 'merk' => 'Komatsu', 'kode' => 'A2' ],
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
