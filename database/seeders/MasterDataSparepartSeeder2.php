<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\KategoriSparepart;
use App\Models\MasterDataSparepart;
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

            $totalSuccess = 0;
            $totalErrors  = 0;

            // Process the first sheet - ATB-HUTANG UNIT ALAT
            $worksheet = $spreadsheet->getSheetByName ( 'ATB-HUTANG UNIT ALAT' );

            if ( $worksheet )
            {
                console ( "Processing sheet: ATB-HUTANG UNIT ALAT" );
                list( $success1, $errors1 ) = $this->processSheetATB ( $worksheet );
                console ( "Sheet processed. Success: {$success1}, Errors: {$errors1}" );

                $totalSuccess += $success1;
                $totalErrors += $errors1;
            }
            else
            {
                $this->addError ( "Sheet 'ATB-HUTANG UNIT ALAT' not found in Excel file" );
            }

            // Process the second sheet - Nilai Sisa Persediaan Rill
            $worksheet = $spreadsheet->getSheetByName ( 'Nilai Sisa Persediaan Rill' );

            if ( $worksheet )
            {
                console ( "Processing sheet: Nilai Sisa Persediaan Rill" );
                list( $success2, $errors2 ) = $this->processSheetPersediaan ( $worksheet );
                console ( "Sheet processed. Success: {$success2}, Errors: {$errors2}" );

                $totalSuccess += $success2;
                $totalErrors += $errors2;
            }
            else
            {
                $this->addError ( "Sheet 'Nilai Sisa Persediaan Rill' not found in Excel file" );
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
     * Process the ATB-HUTANG UNIT ALAT worksheet and create MasterDataSparepart records
     * 
     * @param \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $worksheet
     * @return array [successCount, errorCount]
     */
    protected function processSheetATB ( $worksheet )
    {
        // Get the highest row with data
        $highestRow = $worksheet->getHighestRow ();

        // Assuming the first row is headers, start from row 2
        $data = [];
        for ( $row = 2; $row <= $highestRow; $row++ )
        {
            // Map Excel columns to our data structure
            $kodeValue       = $worksheet->getCell ( 'E' . $row )->getValue ();
            $sparepartValue  = $worksheet->getCell ( 'G' . $row )->getValue ();
            $merkValue       = $worksheet->getCell ( 'H' . $row )->getValue ();
            $partNumberValue = $worksheet->getCell ( 'I' . $row )->getValue ();

            // Skip empty rows
            if ( empty ( $sparepartValue ) )
            {
                continue;
            }

            $data[] = [ 
                'kode'        => $kodeValue,
                'sparepart'   => $sparepartValue,
                'merk'        => $merkValue ?: '-',
                'part_number' => $partNumberValue ?: '-',
            ];
        }

        console ( 'Loaded ' . count ( $data ) . ' records from ATB-HUTANG UNIT ALAT sheet' );

        return $this->processData ( $data );
    }

    /**
     * Process the Nilai Sisa Persediaan Rill worksheet and create MasterDataSparepart records
     * 
     * @param \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $worksheet
     * @return array [successCount, errorCount]
     */
    protected function processSheetPersediaan ( $worksheet )
    {
        // Get the highest row with data
        $highestRow = $worksheet->getHighestRow ();

        // Assuming the first row is headers, start from row 2
        $data = [];
        for ( $row = 2; $row <= $highestRow; $row++ )
        {
            // Map Excel columns to our data structure
            // Correct column structure: Proyek Tanggal Kode Supplier Sparepart Part Number Quantity Satuan Harga Net Sparepart Ori
            $kodeValue         = $worksheet->getCell ( 'C' . $row )->getValue ();
            $supplierValue     = $worksheet->getCell ( 'D' . $row )->getValue ();
            $sparepartValue    = $worksheet->getCell ( 'E' . $row )->getValue ();
            $partNumberValue   = $worksheet->getCell ( 'F' . $row )->getValue ();
            $sparepartOriValue = $worksheet->getCell ( 'K' . $row )->getValue ();

            // Skip empty rows
            if ( empty ( $sparepartValue ) )
            {
                continue;
            }

            // Use Sparepart Ori if available, otherwise use Sparepart
            $finalSparepartName = ! empty ( $sparepartOriValue ) ? $sparepartOriValue : $sparepartValue;

            $data[] = [ 
                'kode'        => $kodeValue,
                'sparepart'   => $finalSparepartName,
                'merk'        => '-', // Merk is not available in this sheet, using default
                'part_number' => $partNumberValue ?: '-',
                'supplier'    => $supplierValue ?: '-', // Store supplier info for logging
            ];
        }

        console ( 'Loaded ' . count ( $data ) . ' records from Nilai Sisa Persediaan Rill sheet' );

        return $this->processData ( $data );
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

            $kategori = null;

            // Find the kategori based on code
            try
            {
                $kategori = KategoriSparepart::where ( 'kode', $item[ 'kode' ] )->first ();

                if ( ! $kategori )
                {
                    $this->addError ( "Category with code {$item[ 'kode' ]} not found." );
                    $errorCount++;
                    continue; // Skip this record
                }
            }
            catch ( \Exception $e )
            {
                $this->addError ( "Error finding category with code {$item[ 'kode' ]}: " . $e->getMessage () );
                $errorCount++;
                continue; // Skip this record
            }

            // Create or find the sparepart
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
                $this->addError ( "Error creating sparepart '{$item[ 'sparepart' ]}': " . $e->getMessage () );
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
