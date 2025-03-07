<?php

namespace Database\Seeders;

use App\Models\Proyek;
use App\Models\AlatProyek;
use App\Models\MasterDataAlat;
use Illuminate\Database\Seeder;
use App\Models\KategoriSparepart;
use App\Models\MasterDataSupplier;
use App\Models\MasterDataSparepart;

class _SidanSeeder extends Seeder
{
    public function run () : void
    {
        // Define Alat data with all equipment codes
        $alatCodes = [];

        // Define Sparepart from Panjar
        $sparepartPanjar = [];

        $sparepartNilaiRiil = [ 
            // CV Cahaya Berkah Sentosa 
            [ 'kode' => 'B12', 'supplier' => 'CV Sawitri Cahaya Traktor', 'nama' => 'Pre Fuel Filter', 'part_number' => '438-5386' ],

            // PT Adhie Usaha Mandiri
            [ 'kode' => 'B12', 'supplier' => 'PT Adhie Usaha Mandiri', 'nama' => 'Fuel Filter', 'part_number' => 'J86-20750' ],
            [ 'kode' => 'B12', 'supplier' => 'PT Adhie Usaha Mandiri', 'nama' => 'Fuel Filter', 'part_number' => '23401-1332L/FC1301' ],
            [ 'kode' => 'B11', 'supplier' => 'PT Adhie Usaha Mandiri', 'nama' => 'Oil Filter', 'part_number' => '6736-51-5142/P558615' ],
            [ 'kode' => 'B11', 'supplier' => 'PT Adhie Usaha Mandiri', 'nama' => 'Oil Filter', 'part_number' => '15607-2190L/P502364' ],
            [ 'kode' => 'B12', 'supplier' => 'PT Adhie Usaha Mandiri', 'nama' => 'Pre Fuel Filter', 'part_number' => 'J8620561' ],
            [ 'kode' => 'B12', 'supplier' => 'PT Adhie Usaha Mandiri', 'nama' => 'Pre Fuel Filter', 'part_number' => '23304-JAC70/SF1307' ],

            // PT Hartono Raya Motor Denpasar
            [ 'kode' => 'A6', 'supplier' => 'PT Hartono Raya Motor Denpasar', 'nama' => 'Alternator Seg', 'part_number' => 'A4001541702' ],
            [ 'kode' => 'B12', 'supplier' => 'PT Hartono Raya Motor Denpasar', 'nama' => 'Fuel Filter', 'part_number' => 'A4000920005' ],
            [ 'kode' => 'B11', 'supplier' => 'PT Hartono Raya Motor Denpasar', 'nama' => 'Oil Filter', 'part_number' => 'A9061800209' ],
            [ 'kode' => 'B12', 'supplier' => 'PT Hartono Raya Motor Denpasar', 'nama' => 'Pre Fuel Filter', 'part_number' => 'A4004770702' ],
            [ 'kode' => 'A5', 'supplier' => 'PT Hartono Raya Motor Denpasar', 'nama' => 'Tapered Roller Bearing', 'part_number' => 'A4009812405' ],
            [ 'kode' => 'A5', 'supplier' => 'PT Hartono Raya Motor Denpasar', 'nama' => 'Tapered Roller Bearing', 'part_number' => 'A4009812505' ],
            [ 'kode' => 'A5', 'supplier' => 'PT Hartono Raya Motor Denpasar', 'nama' => 'Tapered Roller Bearing', 'part_number' => 'A4009814805' ],
            [ 'kode' => 'A5', 'supplier' => 'PT Hartono Raya Motor Denpasar', 'nama' => 'Tapered Roller Bearing', 'part_number' => 'A4009814905' ],
            [ 'kode' => 'A5', 'supplier' => 'PT Hartono Raya Motor Denpasar', 'nama' => 'ZB Tellerad/With Pinion I=5.375/RT4-2', 'part_number' => 'A4003502139' ],

            // PT Sefas Keliantama
            [ 'kode' => 'B21', 'supplier' => 'PT Sefas Keliantama', 'nama' => 'Engine Oil', 'part_number' => '15W-40' ],

            // PT Trakindo Utama
            [ 'kode' => 'B14', 'supplier' => 'PT Trakindo Utama', 'nama' => 'Hydraulic Oil Filter', 'part_number' => '093-7521' ],
            [ 'kode' => 'B14', 'supplier' => 'PT Trakindo Utama', 'nama' => 'Hydraulic Oil Filter Pilot', 'part_number' => '5I-8670' ],
            [ 'kode' => 'A2', 'supplier' => 'PT Trakindo Utama', 'nama' => 'Indicator', 'part_number' => '255-2966' ],

            // UD Yoko Motor
            [ 'kode' => 'B14', 'supplier' => 'UD Yoko Motor', 'nama' => 'Hydraulic Oil Filter', 'part_number' => '465-6506' ],
            [ 'kode' => 'B12', 'supplier' => 'UD Yoko Motor', 'nama' => 'Pre Fuel Filter', 'part_number' => '438-5386' ],
            // [ 'kode' => 'B12', 'supplier' => 'UD Yoko Motor', 'nama' => 'Pre Fuel Filter', 'part_number' => '438-5386' ], // Duplicate but dont remove from code Please
            [ 'kode' => 'B15', 'supplier' => 'UD Yoko Motor', 'nama' => 'Transmission Filter', 'part_number' => '337-5270' ],
            // [ 'kode' => 'B15', 'supplier' => 'UD Yoko Motor', 'nama' => 'Transmission Filter', 'part_number' => '337-5270' ], // Duplicate but dont remove from code Please
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
        $proyek = Proyek::where ( 'nama', 'PEMBANGUNAN BENDUNGAN SIDAN (LANJUTAN)' )->first ();
        if ( ! $proyek )
        {
            console ( "Project 'PEMBANGUNAN BENDUNGAN SIDAN (LANJUTAN)' not found!" );
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
