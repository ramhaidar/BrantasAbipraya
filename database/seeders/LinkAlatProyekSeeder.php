<?php

namespace Database\Seeders;

use App\Models\AlatProyek;
use App\Models\MasterDataAlat;
use App\Models\Proyek;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;

class LinkAlatProyekSeeder extends Seeder
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
        $filePath = storage_path ( 'app/seeds/LinkAlat/Alat_Januari_Formatted.xlsx' );

        if ( ! file_exists ( $filePath ) )
        {
            $this->addError ( "File not found: {$filePath}" );
            $this->outputErrors ();
            return;
        }

        console ( "Reading Excel file: {$filePath}" );

        try
        {
            $reader      = IOFactory::createReader ( 'Xlsx' );
            $spreadsheet = $reader->load ( $filePath );

            // Get all sheet names
            $sheetNames = $spreadsheet->getSheetNames ();
            console ( "Found " . count ( $sheetNames ) . " sheets in the Excel file" );

            // Start database transaction
            DB::beginTransaction ();

            $totalSuccessCount = 0;
            $totalErrorCount   = 0;

            // Process each sheet
            foreach ( $sheetNames as $sheetName )
            {
                console ( "Processing sheet: {$sheetName}" );

                $worksheet = $spreadsheet->getSheetByName ( $sheetName );
                $rows      = $worksheet->toArray ();

                // Skip header row
                $header = array_shift ( $rows );

                // Process the sheet
                list( $successCount, $errorCount ) = $this->processSheet ( $rows, $sheetName );

                $totalSuccessCount += $successCount;
                $totalErrorCount += $errorCount;

                console ( "Sheet {$sheetName} completed. Success: {$successCount}, Errors: {$errorCount}" );
            }

            DB::commit ();
            console ( "All sheets processed. Total Success: {$totalSuccessCount}, Total Errors: {$totalErrorCount}" );

            // Output all collected errors at the end
            $this->outputErrors ();

        }
        catch ( \Exception $e )
        {
            DB::rollBack ();
            Log::error ( "Excel seeding error: " . $e->getMessage () );
            $this->addError ( "Failed to process Excel file: " . $e->getMessage () );
            $this->outputErrors ();
        }
    }

    /**
     * Process data from a worksheet
     * 
     * @param array $rows
     * @param string $sheetName
     * @return array [successCount, errorCount]
     */
    protected function processSheet ( $rows, $sheetName )
    {
        $successCount = 0;
        $errorCount   = 0;

        foreach ( $rows as $rowIndex => $row )
        {
            try
            {
                // Extract data from row
                $proyekNama   = $row[ 1 ] ?? null;
                $kodeAlat     = $row[ 2 ] ?? '-';
                $jenisAlat    = $row[ 3 ] ?? '-';
                $merkAlat     = $row[ 4 ] ?? '-';
                $tipeAlat     = $row[ 5 ] ?? '-';
                $serialNumber = $row[ 6 ] ?? '-';

                if ( empty ( $proyekNama ) || empty ( $kodeAlat ) )
                {
                    $this->addError ( "Sheet {$sheetName}, Row " . ( $rowIndex + 2 ) . ": Skipping row with empty project name or equipment code" );
                    continue;
                }

                // Find or create Proyek
                $proyek = Proyek::firstOrCreate (
                    [ 'nama' => $proyekNama ]
                );

                // Find the alat by code
                $masterDataAlat = MasterDataAlat::where ( 'kode_alat', $kodeAlat )->first ();

                // If alat not found, CREATE a new one
                if ( ! $masterDataAlat )
                {
                    console ( "Creating new equipment with code '{$kodeAlat}'" );

                    $masterDataAlat = MasterDataAlat::create ( [ 
                        'kode_alat'         => $kodeAlat,
                        'jenis_alat'        => $jenisAlat,
                        'merek_alat'        => $merkAlat,
                        'tipe_alat'         => $tipeAlat,
                        'serial_number'     => $serialNumber,
                        'id_proyek_current' => $proyek->id,
                    ] );
                }

                // Check if equipment is already assigned to this project and not removed
                $existingAssignment = AlatProyek::where ( 'id_master_data_alat', $masterDataAlat->id )
                    ->where ( 'id_proyek', $proyek->id )
                    ->whereNull ( 'removed_at' )
                    ->first ();

                if ( ! $existingAssignment )
                {
                    // Create a new AlatProyek entry
                    AlatProyek::create ( [ 
                        'id_master_data_alat' => $masterDataAlat->id,
                        'id_proyek'           => $proyek->id,
                        'assigned_at'         => now (),
                    ] );

                    // Update the current project reference
                    $masterDataAlat->update ( [ 'id_proyek_current' => $proyek->id ] );

                    $successCount++;
                    console ( "Equipment {$kodeAlat} assigned to project {$proyekNama}" );
                }
                else
                {
                    console ( "Equipment {$kodeAlat} already assigned to project {$proyekNama}" );
                }
            }
            catch ( \Exception $e )
            {
                $errorCount++;
                Log::error ( "Error processing row in sheet {$sheetName}: " . json_encode ( $row ) . " - " . $e->getMessage () );
                $this->addError ( "Sheet {$sheetName}, Row " . ( $rowIndex + 2 ) . ": " . $e->getMessage () );
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
