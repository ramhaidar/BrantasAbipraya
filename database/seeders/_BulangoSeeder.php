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
        // Create suppliers
        $suppliers = [ 
            'PT. Galajaya Manado',
            'PT. Centra Global Indo',
            'PT. Sefas Keliantama',
            'CV INDUSTRIALINDO',
            'Galahaya Manado',
            'Industrialindo',
            'Industrialindo Makassar',
            'Trakindo Utama',
            'PT. Maju Megah Trans',
            'PT. LTA Diesel Engine Service'
        ];

        foreach ( $suppliers as $supplierName )
        {
            MasterDataSupplier::firstOrCreate (
                [ 'nama' => $supplierName ],
                [ 'alamat' => '-', 'contact_person' => '-' ]
            );
        }

        // Create spareparts and link with suppliers
        $data = [ 
            [ 'kode' => 'B12', 'supplier' => 'PT. Galajaya Manado', 'nama' => 'Fuel Filter Jimco Dutro', 'part_number' => 'FC1002' ],
            [ 'kode' => 'B14', 'supplier' => 'PT. Centra Global Indo', 'nama' => 'Filter Hydraulic', 'part_number' => 'J86-301801' ],
            [ 'kode' => 'B24', 'supplier' => 'PT. Sefas Keliantama', 'nama' => 'Oli Shell', 'part_number' => 'Spirax S4 CX 50' ],
            [ 'kode' => 'B15', 'supplier' => 'PT. Sefas Keliantama', 'nama' => 'Oli Shell', 'part_number' => 'Spirax S2 A 90' ],
            [ 'kode' => 'B12', 'supplier' => 'CV INDUSTRIALINDO', 'nama' => 'Element Fuel', 'part_number' => '360-8960' ],
            [ 'kode' => 'B12', 'supplier' => 'CV INDUSTRIALINDO', 'nama' => 'Filter AS', 'part_number' => '438-5386' ],
            [ 'kode' => 'B12', 'supplier' => 'Galahaya Manado', 'nama' => 'Filter Racor PC200-8', 'part_number' => '2040TM' ],
            [ 'kode' => 'B14', 'supplier' => 'Industrialindo', 'nama' => 'Filter Breather Tank PC200-8M0', 'part_number' => '421-60-35170' ],
            [ 'kode' => 'B12', 'supplier' => 'Industrialindo', 'nama' => 'Fuel Filter Hino FM260', 'part_number' => 'FC1301' ],
            [ 'kode' => 'B11', 'supplier' => 'Industrialindo', 'nama' => 'Oil Filter D65P', 'part_number' => '600-211-8293' ],
            [ 'kode' => 'B12', 'supplier' => 'Industrialindo', 'nama' => 'Fuel Filter D65P', 'part_number' => '600-311-8293' ],
            [ 'kode' => 'B14', 'supplier' => 'Industrialindo', 'nama' => 'Hydraulic Filter D65P', 'part_number' => '07063-01100' ],
            [ 'kode' => 'B12', 'supplier' => 'Trakindo Utama', 'nama' => 'Element Fuel', 'part_number' => '3608960' ],
            [ 'kode' => 'B11', 'supplier' => 'Industrialindo Makassar', 'nama' => 'Oil Filter Sakai', 'part_number' => 'P550596' ],
            [ 'kode' => 'B12', 'supplier' => 'Industrialindo Makassar', 'nama' => 'Fuel Filter Sakai', 'part_number' => 'J8620220' ],
            [ 'kode' => 'B12', 'supplier' => 'Industrialindo Makassar', 'nama' => 'Fuel Filter Water Separator Sakai', 'part_number' => 'J8620770' ],
            [ 'kode' => 'B12', 'supplier' => 'Galahaya Manado', 'nama' => 'Fuel Pre Filter', 'part_number' => '600-319-5610' ],
            [ 'kode' => 'B12', 'supplier' => 'PT. Galajaya Manado', 'nama' => 'Fuel Filter Cummins', 'part_number' => 'J8620212' ],
            [ 'kode' => 'B12', 'supplier' => 'PT. Galajaya Manado', 'nama' => 'Fuel Filter Water Separator PC200-8', 'part_number' => 'P552040PM' ],
            [ 'kode' => 'B12', 'supplier' => 'PT. Galajaya Manado', 'nama' => 'Fuel Filter Water Separator PC200-8', 'part_number' => 'P505961' ],
            [ 'kode' => 'B12', 'supplier' => 'PT. Galajaya Manado', 'nama' => 'Fuel Filter PC200-8M0', 'part_number' => 'J8621314' ],
            [ 'kode' => 'B11', 'supplier' => 'PT. Galajaya Manado', 'nama' => 'Oil Filter', 'part_number' => 'C1316' ],
            [ 'kode' => 'B3', 'supplier' => 'PT. Maju Megah Trans', 'nama' => 'Ban Luar, Dalam, Flap', 'part_number' => '7.50-16' ],
            [ 'kode' => 'A11', 'supplier' => 'Industrialindo', 'nama' => 'Roller Conveyor', 'part_number' => 'DIA. 75 X 930 MM' ],
            [ 'kode' => 'A11', 'supplier' => 'Industrialindo', 'nama' => 'Roller Conveyor', 'part_number' => 'DIA. 75 X 160 MM' ],
            [ 'kode' => 'A11', 'supplier' => 'Industrialindo', 'nama' => 'Roller Conveyor', 'part_number' => 'DIA. 55 X 170 MM' ],
            [ 'kode' => 'A11', 'supplier' => 'Industrialindo', 'nama' => 'Roller Conveyor', 'part_number' => 'DIA. 75 X 640 MM' ],
            [ 'kode' => 'A11', 'supplier' => 'Industrialindo', 'nama' => 'Roller Conveyor', 'part_number' => 'DIA. 55 X 450 MM' ],
            [ 'kode' => 'A11', 'supplier' => 'Industrialindo', 'nama' => 'Roller Conveyor', 'part_number' => 'DIA. 75 X 700 MM' ],
            [ 'kode' => 'A11', 'supplier' => 'Industrialindo', 'nama' => 'Roller Conveyor', 'part_number' => 'DIA. 55 X 200 MM' ],
            [ 'kode' => 'A11', 'supplier' => 'Industrialindo', 'nama' => 'Roller Conveyor', 'part_number' => 'DIA. 55 X 230 MM' ],
            [ 'kode' => 'A11', 'supplier' => 'Industrialindo', 'nama' => 'Roller Conveyor', 'part_number' => 'DIA. 80 X 200 MM' ],
            [ 'kode' => 'A11', 'supplier' => 'Industrialindo', 'nama' => 'Fastener Belt Conveyor', 'part_number' => '80 CM' ],
            [ 'kode' => 'A11', 'supplier' => 'Industrialindo', 'nama' => 'V-Belt', 'part_number' => 'C125' ],
            [ 'kode' => 'A11', 'supplier' => 'Industrialindo', 'nama' => 'V-Belt', 'part_number' => 'D200' ],
            [ 'kode' => 'A11', 'supplier' => 'Industrialindo', 'nama' => 'Koreksi Harga Diskon 10%', 'part_number' => '-' ],
            [ 'kode' => 'B21', 'supplier' => 'PT. Maju Megah Trans', 'nama' => 'Oli Mesin Pertamina', 'part_number' => 'SC 15W40' ],
            [ 'kode' => 'B11', 'supplier' => 'Industrialindo', 'nama' => 'Oil Filter PC200-8M0', 'part_number' => 'P558615' ],
            [ 'kode' => 'B12', 'supplier' => 'Industrialindo', 'nama' => 'Fuel Filter WA200-5', 'part_number' => 'J8621293' ],
            [ 'kode' => 'B12', 'supplier' => 'Industrialindo', 'nama' => 'Pre Fuel Filter WA200-5', 'part_number' => 'P553004' ],
            [ 'kode' => 'B11', 'supplier' => 'Industrialindo', 'nama' => 'Oil Filter Mitsubishi Fuso', 'part_number' => 'ME130968' ],
            [ 'kode' => 'B12', 'supplier' => 'Industrialindo', 'nama' => 'Fuel Filter Mitsubishi Fuso', 'part_number' => 'ME131989' ],
            [ 'kode' => 'B22', 'supplier' => 'PT. Maju Megah Trans', 'nama' => 'Oli Hidrolik Oertamina', 'part_number' => 'Turalik 52' ],
            [ 'kode' => 'B21', 'supplier' => 'PT. Maju Megah Trans', 'nama' => 'Oli Mesin Pertamina', 'part_number' => 'SC 15W40' ],
            [ 'kode' => 'C1', 'supplier' => 'PT. Maju Megah Trans', 'nama' => 'Kawat Las', 'part_number' => 'LB52-4MM' ],
            [ 'kode' => 'B28', 'supplier' => 'PT. Maju Megah Trans', 'nama' => 'Minyak Rem Seiken', 'part_number' => 'DOT 3' ],
            [ 'kode' => 'A10', 'supplier' => 'PT. Maju Megah Trans', 'nama' => 'Stabilizer Rubber Bushing', 'part_number' => 'N55542-Z2007D' ],
            [ 'kode' => 'A2', 'supplier' => 'PT. LTA Diesel Engine Service', 'nama' => 'Common Rail Injector Service and Repair Kit', 'part_number' => '-' ],
            [ 'kode' => 'A2', 'supplier' => 'PT. LTA Diesel Engine Service', 'nama' => 'Control Valve', 'part_number' => '-' ],
            [ 'kode' => 'A2', 'supplier' => 'PT. LTA Diesel Engine Service', 'nama' => 'Nozzle', 'part_number' => '-' ],
            [ 'kode' => 'A2', 'supplier' => 'PT. LTA Diesel Engine Service', 'nama' => 'Solenoid', 'part_number' => '-' ],
            [ 'kode' => 'A2', 'supplier' => 'PT. LTA Diesel Engine Service', 'nama' => 'To Lapping Spacer', 'part_number' => '-' ],
        ];

        foreach ( $data as $item )
        {
            // Find kategori
            $kategori = KategoriSparepart::where ( 'kode', $item[ 'kode' ] )->first ();
            if ( ! $kategori ) continue;

            // Find or create supplier
            $supplier = MasterDataSupplier::where ( 'nama', $item[ 'supplier' ] )->first ();
            if ( ! $supplier ) continue;

            // Use firstOrCreate to avoid duplicate entries
            $sparepart = MasterDataSparepart::firstOrCreate (
                [ 
                    'nama'        => $item[ 'nama' ],
                    'part_number' => $item[ 'part_number' ] ?? '-'
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
        console ( "Found project: " . $proyek->nama . " (ID: " . $proyek->id . ")" );

        // Define equipment data with all equipment codes
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

        console ( "Looking for " . count ( $alatCodes ) . " equipment records" );

        // Find existing equipment
        $existingAlats = MasterDataAlat::whereIn ( 'kode_alat', $alatCodes )->get ();
        console ( "Found " . $existingAlats->count () . " existing equipment records" );

        // Log all found equipment
        foreach ( $existingAlats as $alat )
        {
            console ( "Found equipment: " . $alat->kode_alat . " (ID: " . $alat->id . ")" );
        }

        // Find missing equipment codes
        $existingCodes = $existingAlats->pluck ( 'kode_alat' )->toArray ();
        $missingCodes  = array_diff ( $alatCodes, $existingCodes );

        if ( count ( $missingCodes ) > 0 )
        {
            console ( "Missing equipment codes: " . implode ( ', ', $missingCodes ) );
        }
        else
        {
            console ( "All equipment codes found in database" );
        }

        // Process each equipment
        foreach ( $existingAlats as $alat )
        {
            // Check if the equipment is already linked to this project
            $existing = AlatProyek::where ( 'id_proyek', $proyek->id )
                ->where ( 'id_master_data_alat', $alat->id )
                ->whereNull ( 'removed_at' )
                ->first ();

            if ( $existing )
            {
                console ( "Equipment " . $alat->kode_alat . " already linked to this project" );
                continue;
            }

            console ( "Linking equipment " . $alat->kode_alat . " to project" );

            try
            {
                // Link equipment to project
                $alatProyek = AlatProyek::create ( [ 
                    'id_proyek'           => $proyek->id,
                    'id_master_data_alat' => $alat->id,
                    'assigned_at'         => now (),
                    'removed_at'          => null
                ] );

                console ( "Successfully created AlatProyek record with ID: " . $alatProyek->id );

                // Update the current project for the equipment
                $alat->update ( [ 
                    'id_proyek_current' => $proyek->id
                ] );

                console ( "Updated equipment's current project" );
            }
            catch ( \Exception $e )
            {
                console ( "ERROR linking equipment: " . $e->getMessage () );
            }
        }

        console ( "Equipment linking process completed" );
    }
}
