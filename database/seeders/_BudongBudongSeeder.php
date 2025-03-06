<?php

namespace Database\Seeders;

use App\Models\Proyek;
use App\Models\AlatProyek;
use App\Models\MasterDataAlat;
use Illuminate\Database\Seeder;
use App\Models\KategoriSparepart;
use App\Models\MasterDataSupplier;
use App\Models\MasterDataSparepart;

class _BudongBudongSeeder extends Seeder
{
    public function run () : void
    {
        // Define Alat data with all equipment codes
        $alatCodes = [ 
            'BL 010-17',
            'BL 028-20',
            'CE 075-20',
            'CE 080-20',
            'CE 081-20',
            'CE 120-20',
            'CE 123-20/B',
            'CE 135-20',
            'DT 087-8',
            'DT 095-8',
            'DT 104-8',
            'DT 105-8',
            'HD 081-15',
            'HD 083-15',
            'HD 091-15',
            'HD 096-15',
            'HD 097-15',
            'HD 099-15',
            'HD 102-15',
            'HD 105-15',
            'HD 130-15',
            'HD 134-15',
            'HD 143-15',
            'HD 162-15',
            'VR 013-10'
        ];

        // Define Sparepart from Panjar
        $sparepartPanjar = [ 
            // AI Diesel
            [ 'kode' => 'A5', 'supplier' => 'AI Diesel', 'nama' => 'Las Pinion Gardan Axor' ],

            // Aneka Teknik 
            [ 'kode' => 'A11', 'supplier' => 'Aneka Teknik', 'nama' => 'Bushing Exca' ],

            // Arfan Ban
            [ 'kode' => 'B3', 'supplier' => 'Arfan Ban', 'nama' => 'Ban dalam 10.00-20' ],

            // Elektronik
            [ 'kode' => 'A6', 'supplier' => 'Elektronik', 'nama' => 'Repair Alternator' ],

            // Mattoangin
            [ 'kode' => 'B3', 'supplier' => 'Mattoangin', 'nama' => 'Ban dalam 23.1-26' ],
            [ 'kode' => 'A7', 'supplier' => 'Mattoangin', 'nama' => 'Hose 1/4 170cm' ],
            [ 'kode' => 'A7', 'supplier' => 'Mattoangin', 'nama' => 'Hose 1/4 230cm' ],
            [ 'kode' => 'A7', 'supplier' => 'Mattoangin', 'nama' => 'Hose 1/4 60cm' ],
            [ 'kode' => 'A7', 'supplier' => 'Mattoangin', 'nama' => 'Oring box' ],
            [ 'kode' => 'A12', 'supplier' => 'Mattoangin', 'nama' => 'Radial Shaft seal' ],
            [ 'kode' => 'A6', 'supplier' => 'Mattoangin', 'nama' => 'Regulator Switch Axor' ],
            [ 'kode' => 'A5', 'supplier' => 'Mattoangin', 'nama' => 'Rotary Shaft Seal Gardan Axor' ],
            [ 'kode' => 'A6', 'supplier' => 'Mattoangin', 'nama' => 'Selenoid Valve dll' ],

            // Sumatra
            [ 'kode' => 'C1', 'supplier' => 'Sumatra', 'nama' => 'LPG dll' ],
            [ 'kode' => 'C1', 'supplier' => 'Sumatra', 'nama' => 'Oksigen' ],

            // Sumber Coklat
            [ 'kode' => 'A12', 'supplier' => 'Sumber Coklat', 'nama' => 'Bearing 33210' ],
            [ 'kode' => 'C1', 'supplier' => 'Sumber Coklat', 'nama' => 'Kepala Aki' ],
            [ 'kode' => 'A12', 'supplier' => 'Sumber Coklat', 'nama' => 'Lahar 32214' ],
            [ 'kode' => 'A12', 'supplier' => 'Sumber Coklat', 'nama' => 'Lahar 33210 dll' ],
            // [ 'kode' => 'A12', 'supplier' => 'Sumber Coklat', 'nama' => 'Lahar 33210 dll' ], // Duplicate but dont remove from code Please
            [ 'kode' => 'A6', 'supplier' => 'Sumber Coklat', 'nama' => 'Lampu Mundur HD dll' ],
            [ 'kode' => 'B22', 'supplier' => 'Sumber Coklat', 'nama' => 'Oli SAE10' ],
            [ 'kode' => 'C1', 'supplier' => 'Sumber Coklat', 'nama' => 'Pompa Grease dll' ],

            // Sumber Coklat Teknik
            [ 'kode' => 'C1', 'supplier' => 'Sumber Coklat Teknik', 'nama' => 'Colokan Las' ],
            [ 'kode' => 'C1', 'supplier' => 'Sumber Coklat Teknik', 'nama' => 'Holder Las dll' ],
            [ 'kode' => 'C1', 'supplier' => 'Sumber Coklat Teknik', 'nama' => 'Mur 24 dll' ],
            [ 'kode' => 'C1', 'supplier' => 'Sumber Coklat Teknik', 'nama' => 'Timah dll' ],
        ];

        // Define Sparepart from Unit Alat
        $sparepartNilaiRiil = [ 
            // CV Industrialindo
            [ 'kode' => 'A11', 'supplier' => 'CV Industrialindo', 'nama' => 'Adaptor', 'part_number' => '20Y-70-14520' ],
            [ 'kode' => 'A12', 'supplier' => 'CV Industrialindo', 'nama' => 'AS RODA', 'part_number' => 'PS125' ],
            [ 'kode' => 'B3', 'supplier' => 'CV Industrialindo', 'nama' => 'Ban Dalam-11', 'part_number' => '7.50-16' ],
            [ 'kode' => 'B3', 'supplier' => 'CV Industrialindo', 'nama' => 'Ban Luar Cacing-11', 'part_number' => '7.50-16' ],
            [ 'kode' => 'A9', 'supplier' => 'CV Industrialindo', 'nama' => 'BOOSTER REM PS125/HDX', 'part_number' => 'PS125' ],
            [ 'kode' => 'B12', 'supplier' => 'CV Industrialindo', 'nama' => 'Cartridge', 'part_number' => '600-311-8321' ],
            [ 'kode' => 'B13', 'supplier' => 'CV Industrialindo', 'nama' => 'FILTER AIR/CORROSION', 'part_number' => '600-411-1191' ],
            [ 'kode' => 'B14', 'supplier' => 'CV Industrialindo', 'nama' => 'FILTER HYDROLIK', 'part_number' => '07063-01054' ],
            [ 'kode' => 'B11', 'supplier' => 'CV Industrialindo', 'nama' => 'Filter Oli Mesin', 'part_number' => '15607-2190L' ],
            [ 'kode' => 'B15', 'supplier' => 'CV Industrialindo', 'nama' => 'Filter Oli Transmisi', 'part_number' => '32915-LVA10' ],
            [ 'kode' => 'B12', 'supplier' => 'CV Industrialindo', 'nama' => 'Filter Solar', 'part_number' => '4587259' ],
            [ 'kode' => 'B3', 'supplier' => 'CV Industrialindo', 'nama' => 'Flap-11', 'part_number' => 'R16' ],
            [ 'kode' => 'B12', 'supplier' => 'CV Industrialindo', 'nama' => 'Fuel Filter', 'part_number' => '1R-0750' ],
            [ 'kode' => 'A9', 'supplier' => 'CV Industrialindo', 'nama' => 'KAMPAS REM HDX', 'part_number' => 'PS125' ],
            [ 'kode' => 'A1', 'supplier' => 'CV Industrialindo', 'nama' => 'Lampu Mundur', 'part_number' => 'PS125' ],
            [ 'kode' => 'B3', 'supplier' => 'CV Industrialindo', 'nama' => 'Marset', 'part_number' => 'R20' ],
            [ 'kode' => 'A12', 'supplier' => 'CV Industrialindo', 'nama' => 'Nut-Washer Belakang Kiri', 'part_number' => 'PS125' ],
            [ 'kode' => 'B11', 'supplier' => 'CV Industrialindo', 'nama' => 'Oil Filter', 'part_number' => 'Sakura' ],
            [ 'kode' => 'B23', 'supplier' => 'CV Industrialindo', 'nama' => 'Oli 90', 'part_number' => 'Valvoline 80W90' ],
            [ 'kode' => 'A10', 'supplier' => 'CV Industrialindo', 'nama' => 'Per Belakang Assembly-11', 'part_number' => 'PS125' ],
            [ 'kode' => 'A10', 'supplier' => 'CV Industrialindo', 'nama' => 'Shockbreaker Depan', 'part_number' => 'PS125' ],
            [ 'kode' => 'A10', 'supplier' => 'CV Industrialindo', 'nama' => 'Shockbreaker Belakang', 'part_number' => 'PS125' ],

            // PT Adhie Usaha Mandiri
            [ 'kode' => 'B13', 'supplier' => 'PT Adhie Usaha Mandiri', 'nama' => 'Air Cleaner-12', 'part_number' => 'P532966' ],
            [ 'kode' => 'B13', 'supplier' => 'PT Adhie Usaha Mandiri', 'nama' => 'Air Cleaner-12', 'part_number' => 'A1088' ],
            [ 'kode' => 'B11', 'supplier' => 'PT Adhie Usaha Mandiri', 'nama' => 'Engine Oil Filter-12', 'part_number' => '6736-51-5142' ],
            [ 'kode' => 'B11', 'supplier' => 'PT Adhie Usaha Mandiri', 'nama' => 'Engine Oil Filter-12', 'part_number' => 'P502008' ],
            [ 'kode' => 'B12', 'supplier' => 'PT Adhie Usaha Mandiri', 'nama' => 'Fuel Filter-12', 'part_number' => '600-319-3750' ],
            [ 'kode' => 'B12', 'supplier' => 'PT Adhie Usaha Mandiri', 'nama' => 'Fuel Filter-12', 'part_number' => 'P552561' ],
            [ 'kode' => 'B12', 'supplier' => 'PT Adhie Usaha Mandiri', 'nama' => 'Fuel Pre Filter-12', 'part_number' => '600-319-5610' ],

            // PT Gala Jaya Mandiri
            [ 'kode' => 'B13', 'supplier' => 'PT Gala Jaya Mandiri', 'nama' => 'Air Cleaner', 'part_number' => '6125-81-7032/P18-1046' ],
            [ 'kode' => 'B16', 'supplier' => 'PT Gala Jaya Mandiri', 'nama' => 'Cartridge (Corrosion Resistor)', 'part_number' => '600-411-1191' ],
            [ 'kode' => 'B14', 'supplier' => 'PT Gala Jaya Mandiri', 'nama' => 'Catride Hvdraulic', 'part_number' => '4211-41001-1/P17-7047' ],
            [ 'kode' => 'B12', 'supplier' => 'PT Gala Jaya Mandiri', 'nama' => 'Element W Sparator', 'part_number' => '4421-39001-0/186-2007' ],
            [ 'kode' => 'B12', 'supplier' => 'PT Gala Jaya Mandiri', 'nama' => 'Filter Solar', 'part_number' => '458758' ],
            [ 'kode' => 'B12', 'supplier' => 'PT Gala Jaya Mandiri', 'nama' => 'Fuel Filter', 'part_number' => '600-311-8293/186-2029' ],
            [ 'kode' => 'B12', 'supplier' => 'PT Gala Jaya Mandiri', 'nama' => 'Fuel Filter', 'part_number' => '4032-09002-0/186-2022' ],
            [ 'kode' => 'B12', 'supplier' => 'PT Gala Jaya Mandiri', 'nama' => 'Fuel Filter', 'part_number' => '600-311-8321' ],
            [ 'kode' => 'B14', 'supplier' => 'PT Gala Jaya Mandiri', 'nama' => 'Hyd rolic Filter', 'part_number' => '07063-01100/P50-7830' ],
            [ 'kode' => 'B11', 'supplier' => 'PT Gala Jaya Mandiri', 'nama' => 'Oil Filter', 'part_number' => '600-211-1231/P55-1670' ],
            [ 'kode' => 'B11', 'supplier' => 'PT Gala Jaya Mandiri', 'nama' => 'Oil Filter', 'part_number' => '4032-64005-0/P55-0596' ],
            [ 'kode' => 'A2', 'supplier' => 'PT Gala Jaya Mandiri', 'nama' => 'V-Belt Set (Cooling Fan)', 'part_number' => '04121-21754' ],

            // PT Sefas Keliantama
            [ 'kode' => 'B26', 'supplier' => 'PT Sefas Keliantama', 'nama' => 'Oli Gardan', 'part_number' => 'Spirax S2 A140' ],
            [ 'kode' => 'B21', 'supplier' => 'PT Sefas Keliantama', 'nama' => 'Oli Rimula 15W40-12', 'part_number' => '15W-40' ],
            [ 'kode' => 'B22', 'supplier' => 'PT Sefas Keliantama', 'nama' => 'Oli Tellus 68-12', 'part_number' => 'Tellus 68' ],
            [ 'kode' => 'B23', 'supplier' => 'PT Sefas Keliantama', 'nama' => 'Oli Transmisi', 'part_number' => 'Spirax S2 G 90' ],
        ];

        // Extract unique suppliers from sparepartNilaiRiil
        $supplierNilaiRiil = [];
        foreach ( $sparepartNilaiRiil as $part )
        {
            if ( ! in_array ( $part[ 'supplier' ], $supplierNilaiRiil ) )
            {
                $supplierNilaiRiil[] = $part[ 'supplier' ];
            }
        }

        // Extract unique suppliers from sparepartPanjar
        $supplierPanjar = [];
        foreach ( $sparepartPanjar as $part )
        {
            if ( ! in_array ( $part[ 'supplier' ], $supplierPanjar ) )
            {
                $supplierPanjar[] = $part[ 'supplier' ];
            }
        }

        // Create MasterDataSupplier records for Panjar suppliers
        foreach ( $supplierPanjar as $supplier )
        {
            // Use firstOrCreate to make idempotent
            MasterDataSupplier::firstOrCreate (
                [ 'nama' => $supplier ],
                [ 
                    'alamat'         => "-",
                    'contact_person' => "- (-)",
                ]
            );
        }

        $suppliers        = array_merge ( $supplierNilaiRiil, $supplierPanjar );
        $missingSuppliers = [];

        // Verify all suppliers exist
        foreach ( $suppliers as $supplierName )
        {
            if ( ! MasterDataSupplier::where ( 'nama', $supplierName )->exists () )
            {
                $missingSuppliers[] = $supplierName;
            }
        }

        if ( ! empty ( $missingSuppliers ) )
        {
            console ( "Missing suppliers: " . implode ( ', ', $missingSuppliers ) );
            return;
        }

        $spareparts = array_merge ( $sparepartPanjar, $sparepartNilaiRiil );

        foreach ( $spareparts as $sparepart )
        {
            // Find kategori
            $kategori = KategoriSparepart::where ( 'kode', $sparepart[ 'kode' ] )->first ();
            if ( ! $kategori ) continue;

            // Find supplier
            $supplier = MasterDataSupplier::where ( 'nama', $sparepart[ 'supplier' ] )->first ();
            if ( ! $supplier )
            {
                console ( "Supplier not found: " . $sparepart[ 'supplier' ] );
                continue;
            }

            // Use firstOrCreate to avoid duplicate entries
            $sparepart = MasterDataSparepart::firstOrCreate (
                [ 
                    'nama'        => $sparepart[ 'nama' ],
                    'part_number' => $sparepart[ 'part_number' ] ?? '-'
                ],
                [ 
                    'merk'                  => '-',
                    'id_kategori_sparepart' => $kategori->id
                ]
            );

            // Link supplier to sparepart if not already linked
            if ( ! $sparepart->masterDataSuppliers ()->where ( 'master_data_supplier.id', $supplier->id )->exists () )
            {
                $sparepart->masterDataSuppliers ()->attach ( $supplier->id );
            }
        }

        // Link equipment to Budong Budong project
        $proyek = Proyek::where ( 'nama', 'BENDUNGAN BUDONG - BUDONG' )->first ();
        if ( ! $proyek )
        {
            console ( "Project 'BENDUNGAN BUDONG - BUDONG' not found!" );
            return;
        }

        // Find existing equipment
        $existingAlats = MasterDataAlat::whereIn ( 'kode_alat', $alatCodes )->get ();

        // Find missing equipment codes
        $existingCodes = $existingAlats->pluck ( 'kode_alat' )->toArray ();
        $missingCodes  = array_diff ( $alatCodes, $existingCodes );

        if ( count ( $missingCodes ) > 0 )
        {
            console ( "Missing equipment codes: " . implode ( ', ', $missingCodes ) );
        }

        // Process each equipment
        foreach ( $existingAlats as $alat )
        {
            if ( AlatProyek::where ( 'id_proyek', $proyek->id )->where ( 'id_master_data_alat', $alat->id )->whereNull ( 'removed_at' )->first () )
            {
                continue;
            }

            try
            {
                // Link equipment to project
                $alatProyek = AlatProyek::create ( [ 
                    'id_proyek'           => $proyek->id,
                    'id_master_data_alat' => $alat->id,
                    'assigned_at'         => now (),
                    'removed_at'          => null
                ] );

                // Update the current project for the equipment
                $alat->update ( [ 
                    'id_proyek_current' => $proyek->id
                ] );
            }
            catch ( \Exception $e )
            {
                console ( "ERROR linking equipment: " . $e->getMessage () );
            }
        }
    }
}
