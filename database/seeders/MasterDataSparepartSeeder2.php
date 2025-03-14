<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\KategoriSparepart;
use App\Models\MasterDataSparepart;
use App\Models\MasterDataSupplier;
use PhpOffice\PhpSpreadsheet\IOFactory;

class MasterDataSparepartSeeder2 extends Seeder
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
        $excelFilePath = storage_path ( 'app/seeds/Sparepart/Januari_Formatted.xlsx' );

        if ( ! file_exists ( $excelFilePath ) )
        {
            $this->addError ( "Excel file not found at: {$excelFilePath}" );
            $this->outputErrors ();
            return;
        }

        try
        {
            // Load the Excel file
            $reader      = IOFactory::createReader ( 'Xlsx' );
            $spreadsheet = $reader->load ( $excelFilePath );

            // First load all suppliers from MASTER sheet
            list( $supplierSuccess, $supplierErrors ) = $this->loadSuppliersFromMasterSheet ( $spreadsheet );
            console ( "Loaded suppliers from MASTER sheet. Success: {$supplierSuccess}, Errors: {$supplierErrors}" );

            $totalSuccess = $supplierSuccess;
            $totalErrors  = $supplierErrors;

            // Define sheets to process
            $sheetsToProcess = [ 
                'Nilai Sisa Persediaan Rill',
                'ATB-HUTANG UNIT ALAT',
                'ATB-PANJAR UNIT ALAT',
                'ATB-PANJAR PROYEK',
                'ATB-MUTASI PROYEK'
            ];

            foreach ( $sheetsToProcess as $sheetName )
            {
                $worksheet = $spreadsheet->getSheetByName ( $sheetName );

                if ( $worksheet )
                {
                    console ( "Processing sheet: {$sheetName}" );
                    // Use the same process method for all sheets since they have the same structure
                    list( $success, $errors ) = $this->processSheet ( $worksheet );
                    console ( "Sheet processed. Success: {$success}, Errors: {$errors}" );

                    $totalSuccess += $success;
                    $totalErrors += $errors;
                }
                else
                {
                    $this->addError ( "Sheet '{$sheetName}' not found in Excel file" );
                }
            }

            console ( "All sheets processed. Total Success: {$totalSuccess}, Total Errors: {$totalErrors}" );
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
     * Process worksheet and create MasterDataSparepart records
     * 
     * @param \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $worksheet
     * @return array [successCount, errorCount]
     */
    protected function processSheet ( $worksheet )
    {
        // Get the sheet name for error reporting
        $sheetName = $worksheet->getTitle ();
        // Get the highest row with data
        $highestRow = $worksheet->getHighestRow ();

        // Assuming the first row is headers, start from row 2
        $data = [];
        for ( $row = 2; $row <= $highestRow; $row++ )
        {
            // Check if row is marked as CONTOH
            $noValue = $worksheet->getCell ( 'A' . $row )->getValue ();
            if ( $noValue === 'CONTOH' )
            {
                continue; // Skip this row
            }

            // Map Excel columns to our data structure
            // #  Proyek  Tanggal  PO  Kode  Supplier  Sparepart  Merk  Part Number  Quantity  Satuan  Harga  Net
            $poValue         = $worksheet->getCell ( 'D' . $row )->getValue (); // Added PO column
            $kodeValue       = $worksheet->getCell ( 'E' . $row )->getValue ();
            $supplierValue   = $worksheet->getCell ( 'F' . $row )->getValue ();
            $sparepartValue  = $worksheet->getCell ( 'G' . $row )->getValue ();
            $merkValue       = $worksheet->getCell ( 'H' . $row )->getValue ();
            $partNumberValue = $worksheet->getCell ( 'I' . $row )->getValue ();

            // Skip empty rows
            if ( empty ( $sparepartValue ) )
            {
                continue;
            }

            // Validate supplier - skip if it looks like a PO number or if it's the same as the PO value
            if ( $this->isPONumber ( $supplierValue ) || $supplierValue == $poValue )
            {
                $supplierValue = '-'; // Reset to default if it looks like a PO
            }

            $data[] = [ 
                'kode'        => $kodeValue,
                'supplier'    => $supplierValue ?: '-',
                'sparepart'   => $sparepartValue,
                'merk'        => $merkValue ?: '-',
                'part_number' => $partNumberValue ?: '-',
                'row'         => $row,
                'sheet_name'  => $sheetName
            ];
        }

        console ( 'Loaded ' . count ( $data ) . ' records from ' . $sheetName );

        return $this->processData ( $data );
    }

    /**
     * Checks if a value appears to be a PO number
     * 
     * @param mixed $value
     * @return bool
     */
    protected function isPONumber ( $value )
    {
        if ( empty ( $value ) ) return false;

        // Check if it's numeric or matches common PO formats
        if ( is_numeric ( $value ) ) return true;

        // Check for common PO patterns (e.g., PO-12345, PO/12345)
        if ( preg_match ( '/^(po|purchase|order|nomor)\s*[-\/:]?\s*\d+/i', $value ) ) return true;

        // Check for date-like formats often used in POs
        if ( preg_match ( '/^\d+[\/\-\.]\d+[\/\-\.]\d+$/', $value ) ) return true;

        return false;
    }

    /**
     * Process data and create MasterDataSparepart records
     * 
     * @param array $data The data to process
     * @return array [successCount, errorCount]
     */
    protected function processData ( $data )
    {
        $successCount     = 0;
        $errorCount       = 0;
        $uniqueSpareparts = [];

        // Process each row from the data
        foreach ( $data as $item )
        {
            // Create a unique key to track duplicates within this import
            $uniqueKey = $item[ 'sparepart' ] . '|' . $item[ 'part_number' ] . '|' . $item[ 'merk' ];

            // Skip if we've already processed this combination in this batch
            if ( isset ( $uniqueSpareparts[ $uniqueKey ] ) )
            {
                console ( "Skipping duplicate: {$item[ 'sparepart' ]}" );
                continue;
            }

            $uniqueSpareparts[ $uniqueKey ] = true;

            $kategori  = null;
            $supplier  = null;
            $sparepart = null;

            // 1. Find or create the supplier
            try
            {
                if ( ! empty ( $item[ 'supplier' ] ) && $item[ 'supplier' ] != '-' )
                {
                    $supplier = MasterDataSupplier::firstOrCreate (
                        [ 'nama' => $item[ 'supplier' ] ],
                        [ 
                            'alamat'         => '-',
                            'contact_person' => '- (-)',
                        ]
                    );

                    console ( "Using supplier: {$item[ 'supplier' ]} (ID: {$supplier->id})" );
                }
            }
            catch ( \Exception $e )
            {
                $this->addError ( "Error creating supplier '{$item[ 'supplier' ]}': " . $e->getMessage () );
                // Continue anyway to create the sparepart
            }

            // 2. Find the kategori based on code
            try
            {
                $kategori = KategoriSparepart::where ( 'kode', $item[ 'kode' ] )->first ();

                if ( ! $kategori )
                {
                    $locationInfo = "Sheet: {$item[ 'sheet_name' ]}, Row: {$item[ 'row' ]}, Code: {$item[ 'kode' ]}, Sparepart: {$item[ 'sparepart' ]}";
                    $this->addError ( "Category with code {$item[ 'kode' ]} not found. Location: {$locationInfo}" );
                    $errorCount++;
                    continue; // Skip this record
                }
            }
            catch ( \Exception $e )
            {
                $locationInfo = "Sheet: {$item[ 'sheet_name' ]}, Row: {$item[ 'row' ]}, Code: {$item[ 'kode' ]}, Sparepart: {$item[ 'sparepart' ]}";
                $this->addError ( "Error finding category with code {$item[ 'kode' ]}: " . $e->getMessage () . ". Location: {$locationInfo}" );
                $errorCount++;
                continue; // Skip this record
            }

            // 3. Create or find the sparepart
            try
            {
                // Check if sparepart already exists with same name and part number
                $existingSparepart = MasterDataSparepart::where ( 'nama', $item[ 'sparepart' ] )
                    ->where ( 'part_number', $item[ 'part_number' ] )
                    ->where ( 'merk', $item[ 'merk' ] )
                    ->first ();

                if ( $existingSparepart )
                {
                    console ( "Sparepart already exists: {$item[ 'sparepart' ]} (ID: {$existingSparepart->id})" );
                    $sparepart = $existingSparepart;
                    $successCount++; // Count as success since we found it
                }
                else
                {
                    // Create new sparepart
                    $sparepart = MasterDataSparepart::create ( [ 
                        'nama'                  => $item[ 'sparepart' ],
                        'part_number'           => $item[ 'part_number' ],
                        'merk'                  => $item[ 'merk' ],
                        'id_kategori_sparepart' => $kategori->id
                    ] );

                    console ( "Created new sparepart: {$item[ 'sparepart' ]} (ID: {$sparepart->id})" );
                    $successCount++;
                }
            }
            catch ( \Exception $e )
            {
                $locationInfo = "Sheet: {$item[ 'sheet_name' ]}, Row: {$item[ 'row' ]}, Sparepart: {$item[ 'sparepart' ]}";
                $this->addError ( "Error creating sparepart '{$item[ 'sparepart' ]}': " . $e->getMessage () . ". Location: {$locationInfo}" );
                $errorCount++;
                continue; // Skip to next record if sparepart creation fails
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
                        console ( "Linked supplier {$supplier->nama} to sparepart {$sparepart->nama}" );
                    }
                    else
                    {
                        console ( "Link between supplier {$supplier->nama} and sparepart {$sparepart->nama} already exists" );
                    }
                }
            }
            catch ( \Exception $e )
            {
                $this->addError ( "Error linking supplier to sparepart: " . $e->getMessage () );
                // Continue anyway since the sparepart was already created
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

    /**
     * Load suppliers from the MASTER sheet
     * 
     * @param \PhpOffice\PhpSpreadsheet\Spreadsheet $spreadsheet
     * @return array [successCount, errorCount]
     */
    protected function loadSuppliersFromMasterSheet ( $spreadsheet )
    {
        $successCount = 0;
        $errorCount   = 0;

        // Try to get the MASTER sheet
        $worksheet = $spreadsheet->getSheetByName ( 'MASTER' );

        if ( ! $worksheet )
        {
            $this->addError ( "Sheet 'MASTER' not found in Excel file" );
            return [ $successCount, $errorCount ];
        }

        console ( "Loading suppliers from MASTER sheet..." );

        // Get the highest row with data
        $highestRow = $worksheet->getHighestRow ();

        // Start from row 3 as requested
        for ( $row = 3; $row <= $highestRow; $row++ )
        {
            $supplierName = $worksheet->getCell ( 'E' . $row )->getValue ();

            // Skip empty supplier names
            if ( empty ( $supplierName ) || $supplierName == '-' )
            {
                continue;
            }

            try
            {
                // Create the supplier
                $supplier = MasterDataSupplier::firstOrCreate (
                    [ 'nama' => $supplierName ],
                    [ 
                        'alamat'         => '-',
                        'contact_person' => '- (-)',
                    ]
                );

                console ( "Added supplier: {$supplierName} (ID: {$supplier->id})" );
                $successCount++;
            }
            catch ( \Exception $e )
            {
                $this->addError ( "Error creating supplier '{$supplierName}' from MASTER sheet: " . $e->getMessage () );
                $errorCount++;
            }
        }

        console ( "Finished loading suppliers: Success: {$successCount}, Errors: {$errorCount}" );

        return [ $successCount, $errorCount ];
    }
}
