<?php

namespace Database\Seeders;

use App\Models\Proyek;
use App\Models\AlatProyek;
use App\Models\MasterDataAlat;
use Illuminate\Database\Seeder;
use App\Models\KategoriSparepart;
use App\Models\MasterDataSupplier;
use App\Models\MasterDataSparepart;

class _BenerPaket4Seeder extends Seeder
{
    public function run () : void
    {
        // Define Alat data with all equipment codes
        $alatCodes = [];

        // Define Sparepart from Panjar
        $sparepartPanjar = [];

        $sparepartNilaiRiil = [ 
            // CV Cahaya Berkah Sentosa 
            [ 'kode' => 'A1', 'supplier' => 'CV Cahaya Berkah Sentosa', 'nama' => 'Blade (Karet Wiper)', 'part_number' => '20Y-54-39450' ],
            [ 'kode' => 'A2', 'supplier' => 'CV Cahaya Berkah Sentosa', 'nama' => 'Feed Pump', 'part_number' => 'Denso' ],
            [ 'kode' => 'A3', 'supplier' => 'CV Cahaya Berkah Sentosa', 'nama' => 'Kampas Kopling', 'part_number' => '-' ],
            [ 'kode' => 'A3', 'supplier' => 'CV Cahaya Berkah Sentosa', 'nama' => 'Rumah Drag Laher', 'part_number' => '31231-1730' ],
            [ 'kode' => 'A5', 'supplier' => 'CV Cahaya Berkah Sentosa', 'nama' => 'Seal (PPC)', 'part_number' => '702-16-71160' ],
            [ 'kode' => 'A5', 'supplier' => 'CV Cahaya Berkah Sentosa', 'nama' => 'Seal (PPC)', 'part_number' => '702-16-71210' ],
            [ 'kode' => 'A6', 'supplier' => 'CV Cahaya Berkah Sentosa', 'nama' => 'Air Aki Tambah 1000ml', 'part_number' => '-' ],
            [ 'kode' => 'A6', 'supplier' => 'CV Cahaya Berkah Sentosa', 'nama' => 'Head Lamp LH', 'part_number' => '-' ],
            [ 'kode' => 'A6', 'supplier' => 'CV Cahaya Berkah Sentosa', 'nama' => 'Head Lamp RH', 'part_number' => '-' ],
            [ 'kode' => 'A6', 'supplier' => 'CV Cahaya Berkah Sentosa', 'nama' => 'Lampu Assy', 'part_number' => '24 Volt' ],
            [ 'kode' => 'A6', 'supplier' => 'CV Cahaya Berkah Sentosa', 'nama' => 'Switch, Pressure (Sensor Oli)', 'part_number' => '6744-81-4010' ],
            [ 'kode' => 'A8', 'supplier' => 'CV Cahaya Berkah Sentosa', 'nama' => 'Bolt Center Per Belakang', 'part_number' => '-' ],
            [ 'kode' => 'A8', 'supplier' => 'CV Cahaya Berkah Sentosa', 'nama' => 'Bolt Center Per Depan', 'part_number' => '-' ],
            [ 'kode' => 'A8', 'supplier' => 'CV Cahaya Berkah Sentosa', 'nama' => 'Tie Rod End LH', 'part_number' => 'S4540-E0090' ],
            [ 'kode' => 'A8', 'supplier' => 'CV Cahaya Berkah Sentosa', 'nama' => 'Tie Rod End RH', 'part_number' => 'S4540-E0090' ],
            [ 'kode' => 'A8', 'supplier' => 'CV Cahaya Berkah Sentosa', 'nama' => 'U-Bolt Per Belakang', 'part_number' => '-' ],
            [ 'kode' => 'A8', 'supplier' => 'CV Cahaya Berkah Sentosa', 'nama' => 'U-Bolt Per Depan', 'part_number' => '-' ],
            [ 'kode' => 'A9', 'supplier' => 'CV Cahaya Berkah Sentosa', 'nama' => 'Cover Sub Assy Drum Dust FR', 'part_number' => '-' ],
            [ 'kode' => 'A9', 'supplier' => 'CV Cahaya Berkah Sentosa', 'nama' => 'Pipa Rem Belakang', 'part_number' => '-' ],
            [ 'kode' => 'A9', 'supplier' => 'CV Cahaya Berkah Sentosa', 'nama' => 'Wheel Cylinder Brake Front', 'part_number' => '-' ],
            [ 'kode' => 'A9', 'supplier' => 'CV Cahaya Berkah Sentosa', 'nama' => 'Wheel Cylinder Brake Rear', 'part_number' => '-' ],
            [ 'kode' => 'A10', 'supplier' => 'CV Cahaya Berkah Sentosa', 'nama' => 'Bolt Dingdong Panjang', 'part_number' => '-' ],
            [ 'kode' => 'A10', 'supplier' => 'CV Cahaya Berkah Sentosa', 'nama' => 'Bolt Dingdong Pendek', 'part_number' => '-' ],
            [ 'kode' => 'A10', 'supplier' => 'CV Cahaya Berkah Sentosa', 'nama' => 'Karet Dingdong', 'part_number' => '49305-11101' ],
            [ 'kode' => 'A11', 'supplier' => 'CV Cahaya Berkah Sentosa', 'nama' => 'BOLT + NUT SHOE', 'part_number' => '-' ],
            [ 'kode' => 'A11', 'supplier' => 'CV Cahaya Berkah Sentosa', 'nama' => 'BOLT CARRIER ROLLER', 'part_number' => '01010-81680' ],
            [ 'kode' => 'A11', 'supplier' => 'CV Cahaya Berkah Sentosa', 'nama' => 'Bolt Shoe', 'part_number' => 'D6R2XL' ],
            [ 'kode' => 'A11', 'supplier' => 'CV Cahaya Berkah Sentosa', 'nama' => 'Tutup Tangki Solar', 'part_number' => '-' ],
            [ 'kode' => 'A11', 'supplier' => 'CV Cahaya Berkah Sentosa', 'nama' => 'WASHER CARRIER ROLLER', 'part_number' => '01643-31645' ],
            [ 'kode' => 'A12', 'supplier' => 'CV Cahaya Berkah Sentosa', 'nama' => 'Bearing - Self - Aligning', 'part_number' => '8G-4189' ],
            [ 'kode' => 'A12', 'supplier' => 'CV Cahaya Berkah Sentosa', 'nama' => 'CARRIER ROLLER', 'part_number' => '20Y-30-00670' ],
            [ 'kode' => 'A12', 'supplier' => 'CV Cahaya Berkah Sentosa', 'nama' => 'Link As - Track Box (satuan)', 'part_number' => '351-0955' ],
            [ 'kode' => 'A12', 'supplier' => 'CV Cahaya Berkah Sentosa', 'nama' => 'Pin', 'part_number' => '281-4015' ],
            [ 'kode' => 'A12', 'supplier' => 'CV Cahaya Berkah Sentosa', 'nama' => 'Roller GP - Track Carrier', 'part_number' => '235-5974' ],
            [ 'kode' => 'A12', 'supplier' => 'CV Cahaya Berkah Sentosa', 'nama' => 'Roller GP - Track Double Flange (Assy)', 'part_number' => '125-4176' ],
            [ 'kode' => 'A12', 'supplier' => 'CV Cahaya Berkah Sentosa', 'nama' => 'Roller GP - Track Single Flange', 'part_number' => '125-4175' ],
            [ 'kode' => 'A12', 'supplier' => 'CV Cahaya Berkah Sentosa', 'nama' => 'Seal Adjuster Track', 'part_number' => '-' ],
            [ 'kode' => 'A12', 'supplier' => 'CV Cahaya Berkah Sentosa', 'nama' => 'Seal As', 'part_number' => '139-0566' ],
            [ 'kode' => 'A12', 'supplier' => 'CV Cahaya Berkah Sentosa', 'nama' => 'SHOE 8MM', 'part_number' => '20Y-32-31320' ],
            [ 'kode' => 'A12', 'supplier' => 'CV Cahaya Berkah Sentosa', 'nama' => 'TRACK LINK RH+LH', 'part_number' => 'MERK ITR PC 200' ],
            [ 'kode' => 'A12', 'supplier' => 'CV Cahaya Berkah Sentosa', 'nama' => 'TRACK ROLLER ASSY', 'part_number' => '20Y-30-07300' ],
            [ 'kode' => 'B11', 'supplier' => 'CV Cahaya Berkah Sentosa', 'nama' => 'CARTRIDGE OIL FILTER', 'part_number' => '5736-51-5142/FL 3349' ],
            [ 'kode' => 'B11', 'supplier' => 'CV Cahaya Berkah Sentosa', 'nama' => 'Oil Filter', 'part_number' => 'I6-97247 514-0' ],
            [ 'kode' => 'B11', 'supplier' => 'CV Cahaya Berkah Sentosa', 'nama' => 'OIL FILTER', 'part_number' => '15607-2190L' ],
            [ 'kode' => 'B11', 'supplier' => 'CV Cahaya Berkah Sentosa', 'nama' => 'OIL FILTER', 'part_number' => 'A9061800209' ],
            [ 'kode' => 'B12', 'supplier' => 'CV Cahaya Berkah Sentosa', 'nama' => 'Fuel Filter', 'part_number' => 'I6-97172 549-0' ],
            [ 'kode' => 'B12', 'supplier' => 'CV Cahaya Berkah Sentosa', 'nama' => 'FUEL FILTER', 'part_number' => '23401-1332L' ],
            [ 'kode' => 'B12', 'supplier' => 'CV Cahaya Berkah Sentosa', 'nama' => 'FUEL FILTER', 'part_number' => '600-319-3750' ],
            [ 'kode' => 'B12', 'supplier' => 'CV Cahaya Berkah Sentosa', 'nama' => 'FUEL FILTER', 'part_number' => 'A400092005/JFE-88022' ],
            [ 'kode' => 'B12', 'supplier' => 'CV Cahaya Berkah Sentosa', 'nama' => 'FUEL PRE FILTER', 'part_number' => '600-319-5610' ],
            [ 'kode' => 'B12', 'supplier' => 'CV Cahaya Berkah Sentosa', 'nama' => 'FUEL PRE FILTER', 'part_number' => 'A4004770702' ],
            [ 'kode' => 'B12', 'supplier' => 'CV Cahaya Berkah Sentosa', 'nama' => 'Water Separator', 'part_number' => '438-5386' ],
            [ 'kode' => 'B12', 'supplier' => 'CV Cahaya Berkah Sentosa', 'nama' => 'Water Separator', 'part_number' => 'J86-20561' ],
            [ 'kode' => 'B12', 'supplier' => 'CV Cahaya Berkah Sentosa', 'nama' => 'WATER SEPARATOR', 'part_number' => '23401-1440L' ],
            [ 'kode' => 'B13', 'supplier' => 'CV Cahaya Berkah Sentosa', 'nama' => 'Air Filter (Element Inner)', 'part_number' => '4419410020' ],
            [ 'kode' => 'B13', 'supplier' => 'CV Cahaya Berkah Sentosa', 'nama' => 'Air Filter (Element Outer)', 'part_number' => '4419410010' ],
            [ 'kode' => 'B13', 'supplier' => 'CV Cahaya Berkah Sentosa', 'nama' => 'HYDRAULIC TANK BREATHER', 'part_number' => '421-60-35170' ],
            [ 'kode' => 'B28', 'supplier' => 'CV Cahaya Berkah Sentosa', 'nama' => 'MINYAK REM', 'part_number' => 'JUMBO' ],
            [ 'kode' => 'C1', 'supplier' => 'CV Cahaya Berkah Sentosa', 'nama' => 'KAIN MAJUN', 'part_number' => 'KATUN' ],
            [ 'kode' => 'C1', 'supplier' => 'CV Cahaya Berkah Sentosa', 'nama' => 'KAWAT LAS LB 52 3,2 MM', 'part_number' => 'LB 3,2 MM' ],
            [ 'kode' => 'C1', 'supplier' => 'CV Cahaya Berkah Sentosa', 'nama' => 'KAWAT LAS LB 52 4,0 MM', 'part_number' => 'LB 4,0 MM' ],

            // CV Cahyadi Sukses Bersama 
            [ 'kode' => 'B3', 'supplier' => 'CV Cahyadi Sukses Bersama', 'nama' => 'FLAP GT', 'part_number' => '20R' ],

            // CV Kurnia Partindo Jaya
            [ 'kode' => 'A12', 'supplier' => 'CV Kurnia Partindo Jaya', 'nama' => 'Guard ( Kiri Kanan )', 'part_number' => '14Y-30-16133' ],
            [ 'kode' => 'A12', 'supplier' => 'CV Kurnia Partindo Jaya', 'nama' => 'Guard, L.H (Welded)', 'part_number' => '14X-30-11292' ],
            [ 'kode' => 'A12', 'supplier' => 'CV Kurnia Partindo Jaya', 'nama' => 'Guard, R.H (Welded)', 'part_number' => '14X-30-11312' ],
            [ 'kode' => 'A12', 'supplier' => 'CV Kurnia Partindo Jaya', 'nama' => 'Track roller double flange', 'part_number' => '14X-30-14122' ],

            // PT Adhie Usaha Mandiri 
            [ 'kode' => 'B13', 'supplier' => 'PT Adhie Usaha Mandiri', 'nama' => 'Air Cleaner', 'part_number' => 'P53-2966' ],
            [ 'kode' => 'B15', 'supplier' => 'PT Adhie Usaha Mandiri', 'nama' => 'Filter Coolant', 'part_number' => 'J8640075' ],
            [ 'kode' => 'B11', 'supplier' => 'PT Adhie Usaha Mandiri', 'nama' => 'Filter Oli', 'part_number' => 'J8611670' ],
            [ 'kode' => 'B12', 'supplier' => 'PT Adhie Usaha Mandiri', 'nama' => 'Fuel Filter', 'part_number' => 'J8621105D' ],
            [ 'kode' => 'B12', 'supplier' => 'PT Adhie Usaha Mandiri', 'nama' => 'FUEL FILTER HINO', 'part_number' => 'P550225' ],
            [ 'kode' => 'B12', 'supplier' => 'PT Adhie Usaha Mandiri', 'nama' => 'Fuel Filter PC200-8M0', 'part_number' => 'J8621314' ], // Duplicate, already seeded in _BulangoSeeder
            [ 'kode' => 'B12', 'supplier' => 'PT Adhie Usaha Mandiri', 'nama' => 'FUEL PRE FILTER PC 200-8MO', 'part_number' => 'J8620561' ],
            [ 'kode' => 'B14', 'supplier' => 'PT Adhie Usaha Mandiri', 'nama' => 'HYDRAULIC FILTER PC 200-8MO', 'part_number' => 'J8630180-1' ],
            [ 'kode' => 'B13', 'supplier' => 'PT Adhie Usaha Mandiri', 'nama' => 'HYDRAULIC TANK BREATHER PC 200-8MO', 'part_number' => 'P502574' ],
            [ 'kode' => 'B11', 'supplier' => 'PT Adhie Usaha Mandiri', 'nama' => 'OIL FILTER HINO', 'part_number' => 'P502364' ],
            [ 'kode' => 'B11', 'supplier' => 'PT Adhie Usaha Mandiri', 'nama' => 'OIL FILTER PC 200-10MO', 'part_number' => '' ],
            [ 'kode' => 'B11', 'supplier' => 'PT Adhie Usaha Mandiri', 'nama' => 'OIL FILTER PC 200-8MO', 'part_number' => 'P558615' ],
            [ 'kode' => 'B12', 'supplier' => 'PT Adhie Usaha Mandiri', 'nama' => 'Water Separator', 'part_number' => 'P50-5961 = (600-319-3610)' ],
            [ 'kode' => 'B12', 'supplier' => 'PT Adhie Usaha Mandiri', 'nama' => 'WATER SEPARATOR HINO', 'part_number' => 'P550730' ],

            // PT Blessindo Prima Sarana 
            [ 'kode' => 'A11', 'supplier' => 'PT Blessindo Prima Sarana', 'nama' => 'Adapter', 'part_number' => '-' ],
            [ 'kode' => 'B13', 'supplier' => 'PT Blessindo Prima Sarana', 'nama' => 'Air Cleaner', 'part_number' => '898321413A' ],
            [ 'kode' => 'B13', 'supplier' => 'PT Blessindo Prima Sarana', 'nama' => 'Air Cleaner Primary', 'part_number' => '6I-2501' ],
            [ 'kode' => 'B13', 'supplier' => 'PT Blessindo Prima Sarana', 'nama' => 'Air Cleaner Secondary', 'part_number' => '6I-2502' ],
            [ 'kode' => 'A12', 'supplier' => 'PT Blessindo Prima Sarana', 'nama' => 'Carrier roller', 'part_number' => '14X-30-00141' ],
            [ 'kode' => 'A10', 'supplier' => 'PT Blessindo Prima Sarana', 'nama' => 'Damper / Rubber Mounting', 'part_number' => '-' ],
            [ 'kode' => 'B14', 'supplier' => 'PT Blessindo Prima Sarana', 'nama' => 'Element As (Transmisi)', 'part_number' => '3375270' ],
            [ 'kode' => 'A12', 'supplier' => 'PT Blessindo Prima Sarana', 'nama' => 'Idler', 'part_number' => '14X-30-00112' ],
            [ 'kode' => 'A9', 'supplier' => 'PT Blessindo Prima Sarana', 'nama' => 'Karet Chamber 24', 'part_number' => 'T24' ],
            [ 'kode' => 'C1', 'supplier' => 'PT Blessindo Prima Sarana', 'nama' => 'Kawat Kuningan untuk Las', 'part_number' => '-' ],
            [ 'kode' => 'A7', 'supplier' => 'PT Blessindo Prima Sarana', 'nama' => 'Slider', 'part_number' => '708-2L-34180' ],
            [ 'kode' => 'A12', 'supplier' => 'PT Blessindo Prima Sarana', 'nama' => 'Track roller double flange', 'part_number' => '14X-30-14122' ],

            // PT Cahaya Surya Kaltara
            [ 'kode' => 'B13', 'supplier' => 'PT Cahaya Surya Kaltara', 'nama' => 'Air Filter (Element Inner)', 'part_number' => 'FR7639' ],
            [ 'kode' => 'B12', 'supplier' => 'PT Cahaya Surya Kaltara', 'nama' => 'Fuel Filter', 'part_number' => 'FK1762 = (1R-0762)' ],
            [ 'kode' => 'B12', 'supplier' => 'PT Cahaya Surya Kaltara', 'nama' => 'Fuel Filter (Main)', 'part_number' => 'FK3750 = (600-319-3750)' ],
            [ 'kode' => 'B13', 'supplier' => 'PT Cahaya Surya Kaltara', 'nama' => 'Fuel Tank Breather', 'part_number' => 'FY5170 = (421-60-35170)' ],
            [ 'kode' => 'B11', 'supplier' => 'PT Cahaya Surya Kaltara', 'nama' => 'Oil Filter', 'part_number' => 'FL3349 = (6735-51-5142)' ],
            [ 'kode' => 'B11', 'supplier' => 'PT Cahaya Surya Kaltara', 'nama' => 'Oil Filter', 'part_number' => 'JOC-88042 = (6742-01-4540)' ],
            [ 'kode' => 'B11', 'supplier' => 'PT Cahaya Surya Kaltara', 'nama' => 'Oil Filter', 'part_number' => '2P4005 = (1R-1808)' ],
            [ 'kode' => 'B11', 'supplier' => 'PT Cahaya Surya Kaltara', 'nama' => 'Oil Filter', 'part_number' => 'JOC-14005 = (15607-2190L)' ],
            [ 'kode' => 'A6', 'supplier' => 'PT Cahaya Surya Kaltara', 'nama' => 'Sensor (Pressure Switch)', 'part_number' => '7861-93-1840' ],
            [ 'kode' => 'B12', 'supplier' => 'PT Cahaya Surya Kaltara', 'nama' => 'Water Separator', 'part_number' => 'JFC-88052 = (600-319-3610)' ],

            // PT Centra Global Indo 
            [ 'kode' => 'A2', 'supplier' => 'PT Centra Global Indo', 'nama' => 'Compressor AC', 'part_number' => '20Y-810-1260' ],
            [ 'kode' => 'A12', 'supplier' => 'PT Centra Global Indo', 'nama' => 'Track Shoe', 'part_number' => '-' ],

            // PT Fortuna Senjaya Abadi
            [ 'kode' => 'B27', 'supplier' => 'PT Fortuna Senjaya Abadi', 'nama' => 'Grease', 'part_number' => '-' ],
            [ 'kode' => 'B24', 'supplier' => 'PT Fortuna Senjaya Abadi', 'nama' => 'Oli Gardan', 'part_number' => 'Rored HAD 140' ],
            [ 'kode' => 'B23', 'supplier' => 'PT Fortuna Senjaya Abadi', 'nama' => 'Oli Transmisi', 'part_number' => 'Rored EPA 90' ],

            // PT Multi Traktor Utama
            [ 'kode' => 'B13', 'supplier' => 'PT Multi Traktor Utama', 'nama' => 'Corrosion Resistor', 'part_number' => '600-411-1151 / P557380' ],
            [ 'kode' => 'B11', 'supplier' => 'PT Multi Traktor Utama', 'nama' => 'Filter Oli', 'part_number' => '600-211-1231 / J8611670' ],
            [ 'kode' => 'B14', 'supplier' => 'PT Multi Traktor Utama', 'nama' => 'Hydraulic Filter', 'part_number' => '07063-01054 / P551054' ],

            // PT Sefas Keliantama
            [ 'kode' => 'B21', 'supplier' => 'PT Sefas Keliantama', 'nama' => 'RIMULA R4 X', 'part_number' => '15W - 40' ],
            [ 'kode' => 'B22', 'supplier' => 'PT Sefas Keliantama', 'nama' => 'TELLUS S2', 'part_number' => 'MX 68' ],

            // PT United Tractors  
            [ 'kode' => 'B14', 'supplier' => 'PT United Tractors', 'nama' => 'Air Filter Element', 'part_number' => '6125-81-7032' ],
            [ 'kode' => 'B14', 'supplier' => 'PT United Tractors', 'nama' => 'Air Filter Element', 'part_number' => '600-181-4300' ],
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
        $proyek = Proyek::where ( 'nama', 'PEMBANGUNAN BENDUNGAN BENER PAKET 4 (MYC)' )->first ();
        if ( ! $proyek )
        {
            console ( "Project 'PEMBANGUNAN BENDUNGAN BENER PAKET 4 (MYC)' not found!" );
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
