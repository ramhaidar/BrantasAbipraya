<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MasterDataSupplier;
use App\Models\LinkSupplierSparepart;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class MasterDataSupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run ()
    {
        // $companies = [ 
        //     "CV Cahaya Berkah Sentosa",
        //     "PT Xpresindo Logistik Utama",
        //     "CV Makindo Wiguna",
        //     "CV Daya Motor Ii",
        //     "PT Dayton Motor Bali",
        //     "CV Sawitri Cahaya Traktor",
        //     "PT Makmur Persada Solusindo",
        //     "PT Maju Megah Trans",
        //     "PT Adhie Usaha Mandiri",
        //     "PT Multicrane Perkasa",
        //     "CV Kencana Multindo Putra",
        //     "PT Mukti Abadi Sarana",
        //     "PT Nihon Pandu Dayatama",
        //     "PT Centra Global Indo",
        //     "PT United Tractors Tbk",
        //     "PT Cahaya Surya Kaltara",
        //     "CV Cahyadi Sukses Bersama",
        //     "PT Diesel Utama Indonesia",
        //     "CV Kurnia Partindo Jaya",
        //     "PT Gala Jaya Banjarmasin",
        //     "CV Sinar Makmur Baru",
        //     "PT Blessindo Prima Sarana",
        //     "PT Sefas Keliantama",
        //     "PT Arjuna Logistik Indonesia",
        //     "CV Industrialindo",
        //     "CV Geronimo Mandiri",
        //     "PT Gala Jaya Mandiri",
        //     "PT Annapurna Jaya Agung",
        //     "PT Gala Jaya Pekanbaru",
        //     "PT Diva Mandiri Semesta",
        //     "PT Mahkota Elang Internusa",
        //     "PT Bukaka Teknik Utama Tbk",
        //     "PT Trakindo Utama",
        //     "CV Harapan Motor",
        //     "PT Equipindo Perkasa",
        //     "PT Sicoma Indo Perkasa",
        //     "PT Vakamindo Mitra Prima",
        //     "PT Hartono Raya Motor",
        //     "UD Yoko Motor"
        // ];

        // foreach ( $companies as $company )
        // {
        //     // Menggunakan factory untuk setiap nama perusahaan
        //     MasterDataSupplier::factory ()->create ( [ 'nama' => $company ] );
        // }

        $real_companies = [ 
            "77 JAYA",
            "FAJRI JAYA MOTOR",
            "ALFIAN MOTOR",
            "AGUNG MAKMUR MOTOR",

            "SUMBER JAYA TEKNIK",
            "PRIMA PARTS INDONESIA",
            "MEGA MOTOR SPAREPART"
        ];

        foreach ( $real_companies as $company )
        {
            // Menggunakan factory untuk setiap nama perusahaan
            MasterDataSupplier::factory ()->create ( [ 'nama' => $company ] );
        }

        LinkSupplierSparepart::create ( [ 
            'id_master_data_supplier'  => 1,
            'id_master_data_sparepart' => 1,
        ] );

        LinkSupplierSparepart::create ( [ 
            'id_master_data_supplier'  => 2,
            'id_master_data_sparepart' => 2,
        ] );

        LinkSupplierSparepart::create ( [ 
            'id_master_data_supplier'  => 2,
            'id_master_data_sparepart' => 3,
        ] );

        LinkSupplierSparepart::create ( [ 
            'id_master_data_supplier'  => 3,
            'id_master_data_sparepart' => 4,
        ] );

        LinkSupplierSparepart::create ( [ 
            'id_master_data_supplier'  => 4,
            'id_master_data_sparepart' => 5,
        ] );

        // ==============================

        LinkSupplierSparepart::create ( [ 
            'id_master_data_supplier'  => 2,
            'id_master_data_sparepart' => 6,
        ] );

        LinkSupplierSparepart::create ( [ 
            'id_master_data_supplier'  => 1,
            'id_master_data_sparepart' => 7,
        ] );

        LinkSupplierSparepart::create ( [ 
            'id_master_data_supplier'  => 1,
            'id_master_data_sparepart' => 8,
        ] );

        LinkSupplierSparepart::create ( [ 
            'id_master_data_supplier'  => 4,
            'id_master_data_sparepart' => 9,
        ] );

        LinkSupplierSparepart::create ( [ 
            'id_master_data_supplier'  => 3,
            'id_master_data_sparepart' => 10,
        ] );

        // Ensure all parts (1-35) have at least one supplier
        for ( $i = 1; $i <= 35; $i++ )
        {
            // Check if part already has a supplier
            $existingLink = LinkSupplierSparepart::where ( 'id_master_data_sparepart', $i )->first ();

            if ( ! $existingLink )
            {
                // If no supplier exists, assign one randomly
                LinkSupplierSparepart::create ( [ 
                    'id_master_data_supplier'  => rand ( 1, 7 ),
                    'id_master_data_sparepart' => $i,
                ] );
            }

            // Add additional suppliers randomly (30% chance for each part)
            if ( rand ( 1, 100 ) <= 30 )
            {
                $existingSuppliers = LinkSupplierSparepart::where ( 'id_master_data_sparepart', $i )
                    ->pluck ( 'id_master_data_supplier' )
                    ->toArray ();

                $newSupplier = rand ( 1, 7 );
                while ( in_array ( $newSupplier, $existingSuppliers ) )
                {
                    $newSupplier = rand ( 1, 7 );
                }

                LinkSupplierSparepart::create ( [ 
                    'id_master_data_supplier'  => $newSupplier,
                    'id_master_data_sparepart' => $i,
                ] );
            }
        }

    }
}
