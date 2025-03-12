<?php

namespace Database\Seeders;

use App\Models\ATB;
use App\Models\Proyek;
use Illuminate\Database\Seeder;
use App\Models\KategoriSparepart;
use App\Models\MasterDataSupplier;
use App\Models\MasterDataSparepart;
use App\Http\Controllers\SaldoController;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ATBSeeder extends Seeder
{
    /**
     * Array to store all error messages
     */
    protected $errorMessages = [];

    /**
     * Run the database seeds.
     */
    public function run () : void
    {
        // Define the path to your Excel file
        $excelFilePath = storage_path ( 'app/seeds/ATB/Desember_Formatted.xlsx' );

        if ( ! file_exists ( $excelFilePath ) )
        {
            $this->addError ( "Excel file not found at: {$excelFilePath}" );
            $this->outputErrors ();
            return;
        }

        // Define sheet names and their corresponding projects
        $sheetConfigs = [ 
            'BUDONG'       => 'BENDUNGAN BUDONG - BUDONG',
            'BULANGO'      => 'PEMBANGUNAN BENDUNGAN BULANGO ULU',
            'BENER4'       => 'PEMBANGUNAN BENDUNGAN BENER PAKET 4 (MYC)',
            'KEUREUTO'     => 'PEMBANGUNAN BENDUNGAN KEUREUTO ACEH',
            'SIDAN'        => 'PEMBANGUNAN BENDUNGAN SIDAN (LANJUTAN)',
            'BINTANG BANO' => 'JARINGAN IRIGASI D.I. BINTANG BANO',
            'JRAGUNG3'     => 'PEMBANGUNAN BENDUNGAN JRAGUNG PAKET 3',
            'MBAY'         => 'PEMBANGUNAN BENDUNGAN MBAY',
            'IKN'          => 'WORKSHOP IKN',
            'PROBOWANGI'   => 'PEMBANGUNAN JALAN TOL PROBOLINGGO - BANYUWANGI PAKET 1',
            'SUBANG'       => 'POOL ALAT SUBANG',
            'KARSA'        => 'PT KARSA PILAR KONSTRUKSI (PERUMAHAN KUPANG)',
            'KARAWANG'     => 'ELEVATED KARAWANG',
            'LEMATANG'     => 'IRIGASI D.I. LEMATANG KOTA PAGAR ALAM PHASE II PAKET 1',
            'BAGONG'       => 'BENDUNGAN BAGONG',
            'KUPANG'       => 'PERUMAHAN KUPANG',
        ];

        try
        {
            // Load the Excel file
            $reader      = IOFactory::createReader ( 'Xlsx' );
            $spreadsheet = $reader->load ( $excelFilePath );

            $totalSuccess = 0;
            $totalErrors  = 0;

            // Process each sheet
            foreach ( $sheetConfigs as $sheetName => $projectName )
            {
                console ( "Processing sheet: {$sheetName} for project: {$projectName}" );

                // Select the specific sheet
                $worksheet = $spreadsheet->getSheetByName ( $sheetName );

                if ( ! $worksheet )
                {
                    $this->addError ( "Sheet '{$sheetName}' not found in Excel file" );
                    continue;
                }

                // Get the project
                $proyek = Proyek::where ( 'nama', $projectName )->first ();

                if ( ! $proyek )
                {
                    $this->addError ( "Project '{$projectName}' not found!" );
                    continue;
                }

                // Process the sheet
                list( $success, $errors ) = $this->processSheet ( $worksheet, $proyek );

                $totalSuccess += $success;
                $totalErrors += $errors;

                console ( "Sheet {$sheetName} completed. Success: {$success}, Errors: {$errors}" );
            }

            console ( "All sheets processed. Total Success: {$totalSuccess}, Total Errors: {$totalErrors}" );

            // Output all collected errors at the end
            $this->outputErrors ();
        }
        catch ( \Exception $e )
        {
            $this->addError ( "Error processing Excel file: " . $e->getMessage () );
            $this->outputErrors ();
            return;
        }
    }

    /**
     * Process a worksheet and create ATB records
     * 
     * @param \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $worksheet
     * @param \App\Models\Proyek $proyek
     * @return array [successCount, errorCount]
     */
    protected function processSheet ( $worksheet, $proyek )
    {
        // Get the highest row with data
        $highestRow = $worksheet->getHighestRow ();

        // Assuming the first row is headers, start from row 2
        $data = [];
        for ( $row = 2; $row <= $highestRow; $row++ )
        {
            // Parse the price value using European number format (e.g. 41.461,030)
            $hargaRaw = $worksheet->getCell ( 'K' . $row )->getValue ();
            $harga    = $hargaRaw;

            if ( is_string ( $hargaRaw ) )
            {
                // Remove spaces
                $harga = trim ( $hargaRaw );
                // Check if negative (format might be like "- 41.461,030")
                $isNegative = strpos ( $harga, '-' ) !== false;
                // Remove all non-numeric characters except . and ,
                $harga = preg_replace ( '/[^0-9.,]/', '', $harga );
                // Replace dots (thousand separators) with nothing
                $harga = str_replace ( '.', '', $harga );
                // Replace comma (decimal separator) with period
                $harga = str_replace ( ',', '.', $harga );
                // Convert to float
                $harga = (float) $harga;
                // Apply negative sign if needed
                if ( $isNegative )
                {
                    $harga = -$harga;
                }
            }

            // Map Excel columns to our data structure
            $data[] = [ 
                'proyek'      => $worksheet->getCell ( 'B' . $row )->getValue (),
                'tanggal'     => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject (
                    $worksheet->getCell ( 'C' . $row )->getValue ()
                )->format ( 'Y-m-d' ),
                'kode'        => $worksheet->getCell ( 'D' . $row )->getValue (),
                'supplier'    => $worksheet->getCell ( 'E' . $row )->getValue (),
                'sparepart'   => $worksheet->getCell ( 'F' . $row )->getValue (),
                'merk'        => $worksheet->getCell ( 'G' . $row )->getValue (),
                'part_number' => $worksheet->getCell ( 'H' . $row )->getValue (),
                'quantity'    => (int) $worksheet->getCell ( 'I' . $row )->getValue (),
                'satuan'      => $worksheet->getCell ( 'J' . $row )->getValue (),
                'harga'       => $harga
            ];
        }

        console ( 'Loaded ' . count ( $data ) . ' records from sheet' );

        $successCount = 0;
        $errorCount   = 0;

        // Create an instance of SaldoController to create Saldo records
        $saldoController = new SaldoController();

        // Process each row from the data
        foreach ( $data as $item )
        {
            $supplier  = null;
            $sparepart = null;
            $kategori  = null;

            // 1. Find or create the supplier
            try
            {
                $supplier = MasterDataSupplier::firstOrCreate (
                    [ 'nama' => $item[ 'supplier' ] ],
                    [ 
                        'alamat'         => '-',
                        'contact_person' => '- (-)',
                    ]
                );
            }
            catch ( \Exception $e )
            {
                $this->addError ( "Error creating supplier '{$item[ 'supplier' ]}': " . $e->getMessage () );
                $errorCount++;
                // Continue anyway to try to create the ATB
            }

            // 2. Find the kategori based on code
            try
            {
                $kategori = KategoriSparepart::where ( 'kode', $item[ 'kode' ] )->first ();

                if ( ! $kategori )
                {
                    $this->addError ( "Category with code {$item[ 'kode' ]} not found." );
                    // Continue anyway to try to create the ATB
                }
            }
            catch ( \Exception $e )
            {
                $this->addError ( "Error finding category with code {$item[ 'kode' ]}: " . $e->getMessage () );
                // Continue anyway to try to create the ATB
            }

            // 3. Find or create the sparepart
            try
            {
                // Check if sparepart already exists
                $existingSparepart = MasterDataSparepart::where ( 'nama', $item[ 'sparepart' ] )
                    ->where ( 'part_number', $item[ 'part_number' ] ?: '-' )
                    ->first ();

                if ( $existingSparepart )
                {
                    console ( "Found existing sparepart: {$item[ 'sparepart' ]} (ID: {$existingSparepart->id})" );
                    $sparepart = $existingSparepart;
                }
                else if ( $kategori )
                {
                    // Only create new sparepart if we have a kategori
                    $sparepart = MasterDataSparepart::create ( [ 
                        'nama'                  => $item[ 'sparepart' ],
                        'part_number'           => $item[ 'part_number' ] ?: '-',
                        'merk'                  => $item[ 'merk' ] ?: '-',
                        'id_kategori_sparepart' => $kategori->id
                    ] );

                    // console ( "Created new sparepart: {$item[ 'sparepart' ]} (ID: {$sparepart->id})" );
                }
            }
            catch ( \Exception $e )
            {
                $this->addError ( "Error finding/creating sparepart '{$item[ 'sparepart' ]}': " . $e->getMessage () );
                // Continue anyway to try to create the ATB
            }

            // 4. Link the sparepart to the supplier if both exist
            try
            {
                if ( $sparepart && $supplier )
                {
                    $linkExists = $sparepart->masterDataSuppliers ()
                        ->where ( 'master_data_supplier.id', $supplier->id )
                        ->exists ();

                    if ( ! $linkExists )
                    {
                        $sparepart->masterDataSuppliers ()->attach ( $supplier->id );
                    }
                }
            }
            catch ( \Exception $e )
            {
                $this->addError ( "Error linking supplier to sparepart: " . $e->getMessage () );
                // Continue anyway to try to create the ATB
            }

            // 5. Create the ATB record if we have the minimum required data
            try
            {
                if ( ! $sparepart )
                {
                    $this->addError ( "Cannot create ATB: Sparepart not created or found" );
                    $errorCount++;
                    continue;
                }

                if ( ! $supplier )
                {
                    $this->addError ( "Cannot create ATB: Supplier not created or found" );
                    $errorCount++;
                    continue;
                }

                // Create ATB record
                $atb = ATB::create ( [ 
                    'tipe'                     => 'hutang-unit-alat',
                    'dokumentasi_foto'         => null,
                    'surat_tanda_terima'       => null,
                    'tanggal'                  => $item[ 'tanggal' ],
                    'quantity'                 => $item[ 'quantity' ],
                    'harga'                    => $item[ 'harga' ],
                    'id_proyek'                => $proyek->id,
                    'id_asal_proyek'           => null,
                    'id_apb_mutasi'            => null,
                    'id_spb'                   => null,
                    'id_detail_spb'            => null,
                    'id_master_data_sparepart' => $sparepart->id,
                    'id_master_data_supplier'  => $supplier->id,
                ] );

                // Create corresponding Saldo record
                $saldoController->store ( [ 
                    'tipe'                     => 'hutang-unit-alat',
                    'quantity'                 => $item[ 'quantity' ],
                    'harga'                    => $item[ 'harga' ],
                    'id_proyek'                => $proyek->id,
                    'id_master_data_sparepart' => $sparepart->id,
                    'id_master_data_supplier'  => $supplier->id,
                    'id_atb'                   => $atb->id,
                    'satuan'                   => $item[ 'satuan' ] ?? '-'
                ] );

                $successCount++;
            }
            catch ( \Exception $e )
            {
                $this->addError ( "Error creating ATB and Saldo records: " . $e->getMessage () );
                $errorCount++;
            }
        }

        return [ $successCount, $errorCount ];
    }

    /**
     * Add an error message to the collection
     * 
     * @param string $message
     * @return void
     */
    protected function addError ( $message )
    {
        $this->errorMessages[] = $message;
    }

    /**
     * Output all collected error messages
     * 
     * @return void
     */
    protected function outputErrors ()
    {
        if ( count ( $this->errorMessages ) > 0 )
        {
            console ( "========== ERROR SUMMARY ==========" );
            foreach ( $this->errorMessages as $index => $message )
            {
                console ( ( $index + 1 ) . ". " . $message );
            }
            console ( "Total Errors: " . count ( $this->errorMessages ) );
            console ( "=================================" );
        }
    }
}
