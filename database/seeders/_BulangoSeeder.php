<?php

namespace Database\Seeders;

use App\Models\Proyek;
use App\Models\AlatProyek;
use App\Models\MasterDataAlat;
use Illuminate\Database\Seeder;
use App\Models\KategoriSparepart;
use App\Models\MasterDataSupplier;
use App\Models\MasterDataSparepart;

class _BulangoSeeder extends Seeder
{
    public function run () : void
    {
        // Define Alat data with all equipment codes
        $alatCodes = [];

        // Define Sparepart from Panjar
        $sparepartPanjar = [];

        $sparepartNilaiRiil = [ 
            // CV Industrialindo
            [ 'kode' => 'B12', 'supplier' => 'CV Industrialindo', 'nama' => 'Element Fuel', 'part_number' => '360-8960' ],
            [ 'kode' => 'A11', 'supplier' => 'CV Industrialindo', 'nama' => 'Fastener Belt Conveyor', 'part_number' => '80 CM' ],
            [ 'kode' => 'B12', 'supplier' => 'CV Industrialindo', 'nama' => 'Filter AS', 'part_number' => '438-5386' ],
            [ 'kode' => 'B14', 'supplier' => 'CV Industrialindo', 'nama' => 'Filter Breather Tank PC200-8M0', 'part_number' => '421-60-35170' ],
            [ 'kode' => 'B12', 'supplier' => 'CV Industrialindo', 'nama' => 'Fuel Filter D65P', 'part_number' => '600-311-8293' ],
            [ 'kode' => 'B12', 'supplier' => 'CV Industrialindo', 'nama' => 'Fuel Filter Hino FM260', 'part_number' => 'FC1301' ],
            [ 'kode' => 'B12', 'supplier' => 'CV Industrialindo', 'nama' => 'Fuel Filter Mitsubishi Fuso', 'part_number' => 'ME131989' ],
            [ 'kode' => 'B12', 'supplier' => 'CV Industrialindo', 'nama' => 'Fuel Filter Sakai', 'part_number' => 'J8620220' ],
            [ 'kode' => 'B12', 'supplier' => 'CV Industrialindo', 'nama' => 'Fuel Filter WA200-5', 'part_number' => 'J8621293' ],
            [ 'kode' => 'B12', 'supplier' => 'CV Industrialindo', 'nama' => 'Fuel Filter Water Separator Sakai', 'part_number' => 'J8620770' ],
            [ 'kode' => 'B14', 'supplier' => 'CV Industrialindo', 'nama' => 'Hydraulic Filter D65P', 'part_number' => '07063-01100' ],
            [ 'kode' => 'A11', 'supplier' => 'CV Industrialindo', 'nama' => 'Koreksi Harga Diskon 10%', 'part_number' => '-' ],
            [ 'kode' => 'B11', 'supplier' => 'CV Industrialindo', 'nama' => 'Oil Filter D65P', 'part_number' => '600-211-8293' ],
            [ 'kode' => 'B11', 'supplier' => 'CV Industrialindo', 'nama' => 'Oil Filter Mitsubishi Fuso', 'part_number' => 'ME130968' ],
            [ 'kode' => 'B11', 'supplier' => 'CV Industrialindo', 'nama' => 'Oil Filter PC200-8M0', 'part_number' => 'P558615' ],
            [ 'kode' => 'B11', 'supplier' => 'CV Industrialindo', 'nama' => 'Oil Filter Sakai', 'part_number' => 'P550596' ],
            [ 'kode' => 'B12', 'supplier' => 'CV Industrialindo', 'nama' => 'Pre Fuel Filter WA200-5', 'part_number' => 'P553004' ],
            [ 'kode' => 'A11', 'supplier' => 'CV Industrialindo', 'nama' => 'Roller Conveyor', 'part_number' => 'DIA. 75 X 930 MM' ],
            [ 'kode' => 'A11', 'supplier' => 'CV Industrialindo', 'nama' => 'Roller Conveyor', 'part_number' => 'DIA. 75 X 160 MM' ],
            [ 'kode' => 'A11', 'supplier' => 'CV Industrialindo', 'nama' => 'Roller Conveyor', 'part_number' => 'DIA. 55 X 170 MM' ],
            [ 'kode' => 'A11', 'supplier' => 'CV Industrialindo', 'nama' => 'Roller Conveyor', 'part_number' => 'DIA. 75 X 640 MM' ],
            [ 'kode' => 'A11', 'supplier' => 'CV Industrialindo', 'nama' => 'Roller Conveyor', 'part_number' => 'DIA. 55 X 450 MM' ],
            [ 'kode' => 'A11', 'supplier' => 'CV Industrialindo', 'nama' => 'Roller Conveyor', 'part_number' => 'DIA. 75 X 700 MM' ],
            [ 'kode' => 'A11', 'supplier' => 'CV Industrialindo', 'nama' => 'Roller Conveyor', 'part_number' => 'DIA. 55 X 200 MM' ],
            [ 'kode' => 'A11', 'supplier' => 'CV Industrialindo', 'nama' => 'Roller Conveyor', 'part_number' => 'DIA. 55 X 230 MM' ],
            [ 'kode' => 'A11', 'supplier' => 'CV Industrialindo', 'nama' => 'Roller Conveyor', 'part_number' => 'DIA. 80 X 200 MM' ],
            [ 'kode' => 'A11', 'supplier' => 'CV Industrialindo', 'nama' => 'V-Belt', 'part_number' => 'C125' ],
            [ 'kode' => 'A11', 'supplier' => 'CV Industrialindo', 'nama' => 'V-Belt', 'part_number' => 'D200' ],

            // PT Centra Global Indo
            [ 'kode' => 'B14', 'supplier' => 'PT Centra Global Indo', 'nama' => 'Filter Hydraulic', 'part_number' => 'J86-301801' ],

            // PT Gala Jaya Mandiri (Manado)
            [ 'kode' => 'B12', 'supplier' => 'PT Gala Jaya Mandiri (Manado)', 'nama' => 'Filter Racor PC200-8', 'part_number' => '2040TM' ],
            [ 'kode' => 'B12', 'supplier' => 'PT Gala Jaya Mandiri (Manado)', 'nama' => 'Fuel Filter Cummins', 'part_number' => 'J8620212' ],
            [ 'kode' => 'B12', 'supplier' => 'PT Gala Jaya Mandiri (Manado)', 'nama' => 'Fuel Filter Jimco Dutro', 'part_number' => 'FC1002' ],
            [ 'kode' => 'B12', 'supplier' => 'PT Gala Jaya Mandiri (Manado)', 'nama' => 'Fuel Filter PC200-8M0', 'part_number' => 'J8621314' ],
            [ 'kode' => 'B12', 'supplier' => 'PT Gala Jaya Mandiri (Manado)', 'nama' => 'Fuel Filter Water Separator PC200-8', 'part_number' => 'P552040PM' ],
            [ 'kode' => 'B12', 'supplier' => 'PT Gala Jaya Mandiri (Manado)', 'nama' => 'Fuel Filter Water Separator PC200-8', 'part_number' => 'P505961' ],
            [ 'kode' => 'B12', 'supplier' => 'PT Gala Jaya Mandiri (Manado)', 'nama' => 'Fuel Pre Filter', 'part_number' => '600-319-5610' ],
            [ 'kode' => 'B11', 'supplier' => 'PT Gala Jaya Mandiri (Manado)', 'nama' => 'Oil Filter', 'part_number' => 'C1316' ],

            // PT LTA Diesel Engine Service
            [ 'kode' => 'A2', 'supplier' => 'PT LTA Diesel Engine Service', 'nama' => 'Common Rail Injector Service and Repair Kit', 'part_number' => '-' ],
            [ 'kode' => 'A2', 'supplier' => 'PT LTA Diesel Engine Service', 'nama' => 'Control Valve', 'part_number' => '-' ],
            [ 'kode' => 'A2', 'supplier' => 'PT LTA Diesel Engine Service', 'nama' => 'Nozzle', 'part_number' => '-' ],
            [ 'kode' => 'A2', 'supplier' => 'PT LTA Diesel Engine Service', 'nama' => 'Solenoid', 'part_number' => '-' ],
            [ 'kode' => 'A2', 'supplier' => 'PT LTA Diesel Engine Service', 'nama' => 'To Lapping Spacer', 'part_number' => '-' ],

            // PT Maju Megah Trans
            [ 'kode' => 'B3', 'supplier' => 'PT Maju Megah Trans', 'nama' => 'Ban Luar, Dalam, Flap', 'part_number' => '7.50-16' ],
            [ 'kode' => 'C1', 'supplier' => 'PT Maju Megah Trans', 'nama' => 'Kawat Las', 'part_number' => 'LB52-4MM' ],
            [ 'kode' => 'B28', 'supplier' => 'PT Maju Megah Trans', 'nama' => 'Minyak Rem Seiken', 'part_number' => 'DOT 3' ],
            [ 'kode' => 'B22', 'supplier' => 'PT Maju Megah Trans', 'nama' => 'Oli Hidrolik Oertamina', 'part_number' => 'Turalik 52' ],
            [ 'kode' => 'B21', 'supplier' => 'PT Maju Megah Trans', 'nama' => 'Oli Mesin Pertamina', 'part_number' => 'SC 15W40' ],
            // [ 'kode' => 'B21', 'supplier' => 'PT Maju Megah Trans', 'nama' => 'Oli Mesin Pertamina', 'part_number' => 'SC 15W40' ], // Duplicate but dont remove from code Please
            [ 'kode' => 'A10', 'supplier' => 'PT Maju Megah Trans', 'nama' => 'Stabilizer Rubber Bushing', 'part_number' => 'N55542-Z2007D' ],

            // PT Sefas Keliantama
            [ 'kode' => 'B24', 'supplier' => 'PT Sefas Keliantama', 'nama' => 'Oli Shell', 'part_number' => 'Spirax S4 CX 50' ],
            [ 'kode' => 'B15', 'supplier' => 'PT Sefas Keliantama', 'nama' => 'Oli Shell', 'part_number' => 'Spirax S2 A 90' ],

            // PT Trakindo Utama
            [ 'kode' => 'B12', 'supplier' => 'PT Trakindo Utama', 'nama' => 'Element Fuel', 'part_number' => '3608960' ],
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
        $proyek = Proyek::where ( 'nama', 'PEMBANGUNAN BENDUNGAN BULANGO ULU' )->first ();
        if ( ! $proyek )
        {
            console ( "Project 'PEMBANGUNAN BENDUNGAN BULANGO ULU' not found!" );
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
