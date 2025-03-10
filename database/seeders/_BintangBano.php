<?php

namespace Database\Seeders;

use App\Models\Proyek;
use App\Models\AlatProyek;
use App\Models\MasterDataAlat;
use Illuminate\Database\Seeder;
use App\Models\KategoriSparepart;
use App\Models\MasterDataSupplier;
use App\Models\MasterDataSparepart;

class _BintangBano extends Seeder
{
    public function run () : void
    {
        // Define Alat data with all equipment codes
        $alatCodes = [];

        // Define Sparepart from Panjar
        $sparepartPanjar = [];

        $sparepartNilaiRiil = [ 
            // CV Cahaya Berkah Sentosa 
            [ 'kode' => 'A12', 'supplier' => 'CV Cahaya Berkah Sentosa', 'nama' => 'Seal Dust', 'part_number' => '20Y-70-23230' ],

            // CV Kurnia Partindo Jaya
            [ 'kode' => 'A4', 'supplier' => 'CV Kurnia Partindo Jaya', 'nama' => 'Baut Roda Belakang LH', 'part_number' => '-' ],
            [ 'kode' => 'A4', 'supplier' => 'CV Kurnia Partindo Jaya', 'nama' => 'Baut Roda Belakang RH', 'part_number' => '-' ],
            [ 'kode' => 'A6', 'supplier' => 'CV Kurnia Partindo Jaya', 'nama' => 'Bearing Alternator', 'part_number' => '605' ],
            [ 'kode' => 'A7', 'supplier' => 'CV Kurnia Partindo Jaya', 'nama' => 'Hose Arm', 'part_number' => '20Y-62-12412' ],
            [ 'kode' => 'A7', 'supplier' => 'CV Kurnia Partindo Jaya', 'nama' => 'Hose Arm', 'part_number' => '206-62-31991' ],
            [ 'kode' => 'A7', 'supplier' => 'CV Kurnia Partindo Jaya', 'nama' => 'Hose Boom', 'part_number' => '07085-006A7' ],
            [ 'kode' => 'A7', 'supplier' => 'CV Kurnia Partindo Jaya', 'nama' => 'Hose Boom', 'part_number' => '20Y-62-53750' ],
            [ 'kode' => 'A10', 'supplier' => 'CV Kurnia Partindo Jaya', 'nama' => 'Kawel Belakang', 'part_number' => '55,56' ],
            [ 'kode' => 'A10', 'supplier' => 'CV Kurnia Partindo Jaya', 'nama' => 'Kawel Depan', 'part_number' => '-' ],
            [ 'kode' => 'A8', 'supplier' => 'CV Kurnia Partindo Jaya', 'nama' => 'Pompa Power Steering', 'part_number' => 'Universal' ],

            // PT Cahaya Surya Kaltara
            [ 'kode' => 'B26', 'supplier' => 'PT Cahaya Surya Kaltara', 'nama' => 'Gotra TM 80-90 GL 5 (Gear)', 'part_number' => 'Panaoil' ],
            [ 'kode' => 'B23', 'supplier' => 'PT Cahaya Surya Kaltara', 'nama' => 'Unitrans HD 30 (Transmisi)', 'part_number' => 'Panaoil' ],

            // PT Dayton Motor Bali
            [ 'kode' => 'B3', 'supplier' => 'PT Dayton Motor Bali', 'nama' => 'Ban Dalam', 'part_number' => '1100-20' ],
            [ 'kode' => 'B3', 'supplier' => 'PT Dayton Motor Bali', 'nama' => 'Flap', 'part_number' => '1100-20' ],

            // PT Diesel Utama
            [ 'kode' => 'A2', 'supplier' => 'PT Diesel Utama', 'nama' => 'Coolant Hose', 'part_number' => 'A4005010382' ],

            // PT Fortuna Senjaya Abadi
            [ 'kode' => 'B22', 'supplier' => 'PT Fortuna Senjaya Abadi', 'nama' => 'Oli Hydraulik', 'part_number' => 'S10W' ],
            [ 'kode' => 'B21', 'supplier' => 'PT Fortuna Senjaya Abadi', 'nama' => 'Oli Mesin', 'part_number' => 'Meditran S40' ],
            [ 'kode' => 'B21', 'supplier' => 'PT Fortuna Senjaya Abadi', 'nama' => 'Oli Mesin', 'part_number' => 'S40' ],

            // PT Sefas Keliantama
            [ 'kode' => 'B21', 'supplier' => 'PT Sefas Keliantama', 'nama' => 'Engine Oil', 'part_number' => 'SAE 15W-40' ],
            [ 'kode' => 'B21', 'supplier' => 'PT Sefas Keliantama', 'nama' => 'Oli Mesin', 'part_number' => 'Rimula R4 X 15W-40' ],

            // PT Trakindo Utama
            [ 'kode' => 'B12', 'supplier' => 'PT Trakindo Utama', 'nama' => 'Filter', 'part_number' => '168878' ],

            // UD Yoko Motor
            [ 'kode' => 'A1', 'supplier' => 'UD Yoko Motor', 'nama' => 'Damper', 'part_number' => '20Y-54-71182' ],
            [ 'kode' => 'A2', 'supplier' => 'UD Yoko Motor', 'nama' => 'Fuel Spin On', 'part_number' => '-' ],
            [ 'kode' => 'A2', 'supplier' => 'UD Yoko Motor', 'nama' => 'Priming Pump', 'part_number' => '092130-0220' ],
            [ 'kode' => 'A2', 'supplier' => 'UD Yoko Motor', 'nama' => 'Switch Starter/Selenoid Starter', 'part_number' => '-' ],
            [ 'kode' => 'A3', 'supplier' => 'UD Yoko Motor', 'nama' => 'Bearing Tindis', 'part_number' => 'CT 70B' ],
            [ 'kode' => 'A3', 'supplier' => 'UD Yoko Motor', 'nama' => 'Kampas Kopling', 'part_number' => 'ME 521056' ],
            [ 'kode' => 'A5', 'supplier' => 'UD Yoko Motor', 'nama' => 'Mur Pinion Gardan Tengah', 'part_number' => 'Kepala Kunci 65' ],
            [ 'kode' => 'A5', 'supplier' => 'UD Yoko Motor', 'nama' => 'Seal Roda Depan Dalam', 'part_number' => '130 x 150 x 14' ],
            [ 'kode' => 'A6', 'supplier' => 'UD Yoko Motor', 'nama' => 'IC Regulator', 'part_number' => 'IVR-256' ],
            [ 'kode' => 'A7', 'supplier' => 'UD Yoko Motor', 'nama' => 'Cable Dump', 'part_number' => '5.5 meter' ],
            [ 'kode' => 'A9', 'supplier' => 'UD Yoko Motor', 'nama' => 'Kampas Hand Rem', 'part_number' => '-' ],
            [ 'kode' => 'A9', 'supplier' => 'UD Yoko Motor', 'nama' => 'Relay Emergensi Valve', 'part_number' => 'J08C J08E' ],
            [ 'kode' => 'A12', 'supplier' => 'UD Yoko Motor', 'nama' => 'Bolt', 'part_number' => '20Y-27-11561' ],
            [ 'kode' => 'A12', 'supplier' => 'UD Yoko Motor', 'nama' => 'Canter Pir Belakang', 'part_number' => '-' ],
            [ 'kode' => 'A12', 'supplier' => 'UD Yoko Motor', 'nama' => 'Idler GP-Track', 'part_number' => '190-1546' ],
            [ 'kode' => 'A12', 'supplier' => 'UD Yoko Motor', 'nama' => 'Seal Dust', 'part_number' => '707-56-70540' ],
            [ 'kode' => 'A12', 'supplier' => 'UD Yoko Motor', 'nama' => 'Sprocket', 'part_number' => '20Y-27-11582' ],
            [ 'kode' => 'A12', 'supplier' => 'UD Yoko Motor', 'nama' => 'Washer-Hard', 'part_number' => 'SP-8247' ],
            [ 'kode' => 'A13', 'supplier' => 'UD Yoko Motor', 'nama' => 'Seal Kit', 'part_number' => '05815162' ],
            [ 'kode' => 'B11', 'supplier' => 'UD Yoko Motor', 'nama' => 'Filter Oli', 'part_number' => '15607-2190L' ],
            [ 'kode' => 'B11', 'supplier' => 'UD Yoko Motor', 'nama' => 'Filter Oli', 'part_number' => 'ME130968' ],
            [ 'kode' => 'B11', 'supplier' => 'UD Yoko Motor', 'nama' => 'Filter Oli', 'part_number' => '426-1171' ],
            [ 'kode' => 'B11', 'supplier' => 'UD Yoko Motor', 'nama' => 'Oil Filter Sakai', 'part_number' => '4032-64005-0' ],
            [ 'kode' => 'B12', 'supplier' => 'UD Yoko Motor', 'nama' => 'Filter Solar', 'part_number' => '23401-1332L' ],
            [ 'kode' => 'B12', 'supplier' => 'UD Yoko Motor', 'nama' => 'Filter Solar', 'part_number' => 'C-1007, ME-074013' ],
            [ 'kode' => 'B12', 'supplier' => 'UD Yoko Motor', 'nama' => 'Filter Solar', 'part_number' => 'J86 20220, PSS-0057' ],
            [ 'kode' => 'B12', 'supplier' => 'UD Yoko Motor', 'nama' => 'Filter Solar', 'part_number' => 'P550391' ],
            [ 'kode' => 'B12', 'supplier' => 'UD Yoko Motor', 'nama' => 'Filter Tambahan', 'part_number' => '360-8960' ],
            [ 'kode' => 'B12', 'supplier' => 'UD Yoko Motor', 'nama' => 'Fuel Filter', 'part_number' => '-' ],
            [ 'kode' => 'B12', 'supplier' => 'UD Yoko Motor', 'nama' => 'Fuel Filter Sakai', 'part_number' => '4032-09002-0' ],
            [ 'kode' => 'B12', 'supplier' => 'UD Yoko Motor', 'nama' => 'Water Separator Sakai', 'part_number' => '4421-39001-0' ],
            [ 'kode' => 'B12', 'supplier' => 'UD Yoko Motor', 'nama' => 'Water Sparator', 'part_number' => '23401-1440L' ],
            [ 'kode' => 'B13', 'supplier' => 'UD Yoko Motor', 'nama' => 'Filter Udara', 'part_number' => '17801-JAA10A' ],
            [ 'kode' => 'B13', 'supplier' => 'UD Yoko Motor', 'nama' => 'Filter Udara', 'part_number' => '252-5001 / CP 23210' ],
            [ 'kode' => 'B13', 'supplier' => 'UD Yoko Motor', 'nama' => 'Filter Udara', 'part_number' => '252-5002 / CF 2135' ],
            [ 'kode' => 'B13', 'supplier' => 'UD Yoko Motor', 'nama' => 'Filter Udara', 'part_number' => 'A-1026' ],
            [ 'kode' => 'B14', 'supplier' => 'UD Yoko Motor', 'nama' => 'Filter Hydraulic', 'part_number' => '1G-8878' ],
            [ 'kode' => 'B14', 'supplier' => 'UD Yoko Motor', 'nama' => 'Filter Hydraulic', 'part_number' => '9T-8578' ],
            [ 'kode' => 'B14', 'supplier' => 'UD Yoko Motor', 'nama' => 'Hydraulic Filter', 'part_number' => '4211-41001-1' ],
            [ 'kode' => 'B16', 'supplier' => 'UD Yoko Motor', 'nama' => 'Filter Gardan', 'part_number' => '15607-2060L' ],
            [ 'kode' => 'B28', 'supplier' => 'UD Yoko Motor', 'nama' => 'Minyak Rem', 'part_number' => 'Prestone' ],
            [ 'kode' => 'B28', 'supplier' => 'UD Yoko Motor', 'nama' => 'Oli Power Steering', 'part_number' => 'Jumbo' ],
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
        $proyek = Proyek::where ( 'nama', 'JARINGAN IRIGASI D.I. BINTANG BANO' )->first ();
        if ( ! $proyek )
        {
            console ( "Project 'JARINGAN IRIGASI D.I. BINTANG BANO' not found!" );
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
