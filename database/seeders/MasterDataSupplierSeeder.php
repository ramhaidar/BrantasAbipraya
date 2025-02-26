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
        $real_companies = [ 
            "77 JAYA",
            "FAJRI JAYA MOTOR",
            "ALFIAN MOTOR",
            "AGUNG MAKMUR MOTOR",

            "PT. TRAKINDO UTAMA", // Dealer resmi Caterpillar di Indonesia
            "PT. UNITED TRACTORS Tbk", // Dealer resmi Komatsu di Indonesia
            "PT. HEXINDO ADIPERKASA Tbk", // Dealer resmi Hitachi di Indonesia
            "PT. KOBEXINDO TRACTORS Tbk", // Dealer resmi Doosan di Indonesia
            "CV. WIJAYA PARTS SOLUTION", // Supplier parts general
            "PT. GAMMA DIESEL PARTS INDONESIA", // Spesialis parts diesel
        ];

        foreach ( $real_companies as $company )
        {
            // Use firstOrCreate to make idempotent
            MasterDataSupplier::firstOrCreate (
                [ 'nama' => $company ],
                [ 
                    'alamat'         => 'Alamat ' . $company,
                    'contact_person' => 'CP ' . $company
                ]
            );
        }

        // Define specific supplier-sparepart links
        $specificLinks = [ 
            [ 'supplier' => 1, 'sparepart' => 1 ],
            [ 'supplier' => 2, 'sparepart' => 2 ],
            [ 'supplier' => 2, 'sparepart' => 3 ],
            [ 'supplier' => 3, 'sparepart' => 4 ],
            [ 'supplier' => 4, 'sparepart' => 5 ],
            [ 'supplier' => 2, 'sparepart' => 6 ],
            [ 'supplier' => 1, 'sparepart' => 7 ],
            [ 'supplier' => 1, 'sparepart' => 8 ],
            [ 'supplier' => 4, 'sparepart' => 9 ],
            [ 'supplier' => 3, 'sparepart' => 10 ]
        ];

        foreach ( $specificLinks as $link )
        {
            // Check if the link already exists
            $exists = LinkSupplierSparepart::where ( 'id_master_data_supplier', $link[ 'supplier' ] )
                ->where ( 'id_master_data_sparepart', $link[ 'sparepart' ] )
                ->exists ();

            if ( ! $exists )
            {
                LinkSupplierSparepart::create ( [ 
                    'id_master_data_supplier'  => $link[ 'supplier' ],
                    'id_master_data_sparepart' => $link[ 'sparepart' ]
                ] );
            }
        }

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

                // Find a supplier that isn't already linked
                $newSupplier = rand ( 1, 7 );
                $attempts    = 0;

                // Only try 10 times to avoid infinite loop if all suppliers are already linked
                while ( in_array ( $newSupplier, $existingSuppliers ) && $attempts < 10 )
                {
                    $newSupplier = rand ( 1, 7 );
                    $attempts++;
                }

                // Only create if we found an unlinked supplier
                if ( ! in_array ( $newSupplier, $existingSuppliers ) )
                {
                    LinkSupplierSparepart::create ( [ 
                        'id_master_data_supplier'  => $newSupplier,
                        'id_master_data_sparepart' => $i,
                    ] );
                }
            }
        }
    }
}
