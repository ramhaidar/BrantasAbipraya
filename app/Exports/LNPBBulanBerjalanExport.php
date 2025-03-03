<?php

namespace App\Exports;

use App\Models\ATB;
use App\Models\APB;
use App\Models\Proyek;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LNPBBulanBerjalanExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithCustomStartCell, WithEvents, WithStrictNullComparison
{
    protected $proyekId;
    protected $startDate;
    protected $endDate;
    protected $data;
    protected $sums;

    public function __construct ( $proyekId = null, $startDate = null, $endDate = null )
    {
        $this->proyekId  = $proyekId;
        $this->startDate = $startDate ?? now ()->subMonth ()->day ( 26 );
        $this->endDate   = $endDate ?? now ()->day ( 25 );
        $this->initializeData ();
        $this->calculateSums ();
    }

    private function initializeData ()
    {
        $this->data = [ 
            [ 'kode' => 'A1', 'nama' => 'CABIN', 'jenis' => 'Perbaikan', 'subJenis' => null ],
            [ 'kode' => 'A2', 'nama' => 'ENGINE SYSTEM', 'jenis' => 'Perbaikan', 'subJenis' => null ],
            [ 'kode' => 'A3', 'nama' => 'TRANSMISSION SYSTEM', 'jenis' => 'Perbaikan', 'subJenis' => null ],
            [ 'kode' => 'A4', 'nama' => 'CHASSIS & SWING MACHINERY', 'jenis' => 'Perbaikan', 'subJenis' => null ],
            [ 'kode' => 'A5', 'nama' => 'DIFFERENTIAL SYSTEM', 'jenis' => 'Perbaikan', 'subJenis' => null ],
            [ 'kode' => 'A6', 'nama' => 'ELECTRICAL SYSTEM', 'jenis' => 'Perbaikan', 'subJenis' => null ],
            [ 'kode' => 'A7', 'nama' => 'HYDRAULIC/PNEUMATIC SYSTEM', 'jenis' => 'Perbaikan', 'subJenis' => null ],
            [ 'kode' => 'A8', 'nama' => 'STEERING SYSTEM', 'jenis' => 'Perbaikan', 'subJenis' => null ],
            [ 'kode' => 'A9', 'nama' => 'BRAKE SYSTEM', 'jenis' => 'Perbaikan', 'subJenis' => null ],
            [ 'kode' => 'A10', 'nama' => 'SUSPENSION', 'jenis' => 'Perbaikan', 'subJenis' => null ],
            [ 'kode' => 'A11', 'nama' => 'WORK EQUIPMENT', 'jenis' => 'Perbaikan', 'subJenis' => null ],
            [ 'kode' => 'A12', 'nama' => 'UNDERCARRIAGE', 'jenis' => 'Perbaikan', 'subJenis' => null ],
            [ 'kode' => 'A13', 'nama' => 'FINAL DRIVE', 'jenis' => 'Perbaikan', 'subJenis' => null ],
            [ 'kode' => 'A14', 'nama' => 'FREIGHT COST', 'jenis' => 'Perbaikan', 'subJenis' => null ],
            [ 'kode' => 'B11', 'nama' => 'Oil Filter', 'jenis' => 'Pemeliharaan', 'subJenis' => 'MAINTENANCE KIT' ],
            [ 'kode' => 'B12', 'nama' => 'Fuel Filter', 'jenis' => 'Pemeliharaan', 'subJenis' => 'MAINTENANCE KIT' ],
            [ 'kode' => 'B13', 'nama' => 'Air Filter', 'jenis' => 'Pemeliharaan', 'subJenis' => 'MAINTENANCE KIT' ],
            [ 'kode' => 'B14', 'nama' => 'Hydraulic Filter', 'jenis' => 'Pemeliharaan', 'subJenis' => 'MAINTENANCE KIT' ],
            [ 'kode' => 'B15', 'nama' => 'Transmission Filter', 'jenis' => 'Pemeliharaan', 'subJenis' => 'MAINTENANCE KIT' ],
            [ 'kode' => 'B16', 'nama' => 'Differential Filter', 'jenis' => 'Pemeliharaan', 'subJenis' => 'MAINTENANCE KIT' ],
            [ 'kode' => 'B21', 'nama' => 'Engine Oil', 'jenis' => 'Pemeliharaan', 'subJenis' => 'OIL & LUBRICANTS' ],
            [ 'kode' => 'B22', 'nama' => 'Hydraulic Oil', 'jenis' => 'Pemeliharaan', 'subJenis' => 'OIL & LUBRICANTS' ],
            [ 'kode' => 'B23', 'nama' => 'Transmission Oil', 'jenis' => 'Pemeliharaan', 'subJenis' => 'OIL & LUBRICANTS' ],
            [ 'kode' => 'B24', 'nama' => 'Final Drive Oil', 'jenis' => 'Pemeliharaan', 'subJenis' => 'OIL & LUBRICANTS' ],
            [ 'kode' => 'B25', 'nama' => 'Swing & Damper Oil', 'jenis' => 'Pemeliharaan', 'subJenis' => 'OIL & LUBRICANTS' ],
            [ 'kode' => 'B26', 'nama' => 'Differential Oil', 'jenis' => 'Pemeliharaan', 'subJenis' => 'OIL & LUBRICANTS' ],
            [ 'kode' => 'B27', 'nama' => 'Grease', 'jenis' => 'Pemeliharaan', 'subJenis' => 'OIL & LUBRICANTS' ],
            [ 'kode' => 'B28', 'nama' => 'Brake & Power Steering Fluid', 'jenis' => 'Pemeliharaan', 'subJenis' => 'OIL & LUBRICANTS' ],
            [ 'kode' => 'B29', 'nama' => 'Coolant', 'jenis' => 'Pemeliharaan', 'subJenis' => 'OIL & LUBRICANTS' ],
            [ 'kode' => 'B3', 'nama' => 'TYRE', 'jenis' => 'Pemeliharaan', 'subJenis' => null ],
            [ 'kode' => 'C1', 'nama' => 'WORKSHOP', 'jenis' => 'Workshop', 'subJenis' => null ],
        ];
    }

    private function calculateSums ()
    {
        // Get ATB and APB data for the specified period and project
        $ATB_Data = ATB::with ( 'masterDataSparepart.KategoriSparepart' )
            ->when ( $this->proyekId, function ($query)
            {
                return $query->where ( 'id_proyek', $this->proyekId );
            } )
            ->whereBetween ( 'tanggal', [ $this->startDate, $this->endDate ] )
            ->get ();

        $APB_Data = APB::with ( 'masterDataSparepart.KategoriSparepart' )
            ->when ( $this->proyekId, function ($query)
            {
                return $query->where ( 'id_proyek', $this->proyekId );
            } )
            ->whereBetween ( 'tanggal', [ $this->startDate, $this->endDate ] )
            ->get ();

        $this->sums = [];

        foreach ( $this->data as $category )
        {
            // Filter ATB data for this category
            $categoryItemsATB = $ATB_Data->filter ( function ($item) use ($category)
            {
                return $item->masterDataSparepart &&
                    $item->masterDataSparepart->kategoriSparepart &&
                    strtoupper ( trim ( $item->masterDataSparepart->kategoriSparepart->kode ) ) === strtoupper ( trim ( $category[ 'kode' ] ) );
            } );

            // Filter APB data for this category
            $categoryItemsAPB = $APB_Data->filter ( function ($item) use ($category)
            {
                return $item->masterDataSparepart &&
                    $item->masterDataSparepart->kategoriSparepart &&
                    strtoupper ( trim ( $item->masterDataSparepart->kategoriSparepart->kode ) ) === strtoupper ( trim ( $category[ 'kode' ] ) );
            } );

            // Initialize the category data structure
            $this->sums[ $category[ 'kode' ] ] = [ 
                'nama'     => $category[ 'nama' ],
                'jenis'    => $category[ 'jenis' ],
                'subJenis' => $category[ 'subJenis' ],
                'atb'      => [ 
                    'hutang-unit-alat' => 0,
                    'panjar-unit-alat' => 0,
                    'mutasi-proyek'    => 0,
                    'panjar-proyek'    => 0,
                    'total'            => 0
                ],
                'apb'      => [ 
                    'hutang-unit-alat' => 0,
                    'panjar-unit-alat' => 0,
                    'mutasi-proyek'    => 0,
                    'panjar-proyek'    => 0,
                    'total'            => 0
                ],
                'saldo'    => [ 
                    'hutang-unit-alat' => 0,
                    'panjar-unit-alat' => 0,
                    'mutasi-proyek'    => 0,
                    'panjar-proyek'    => 0,
                    'total'            => 0
                ]
            ];

            // Calculate ATB values by tipe (using the same approach as in LaporanLNPBBulanBerjalanController)
            $this->sums[ $category[ 'kode' ] ][ 'atb' ][ 'hutang-unit-alat' ] = $categoryItemsATB->where ( 'tipe', 'hutang-unit-alat' )->sum ( function ($item)
            {
                return $item->quantity * $item->harga;
            } );

            $this->sums[ $category[ 'kode' ] ][ 'atb' ][ 'panjar-unit-alat' ] = $categoryItemsATB->where ( 'tipe', 'panjar-unit-alat' )->sum ( function ($item)
            {
                return $item->quantity * $item->harga;
            } );

            $this->sums[ $category[ 'kode' ] ][ 'atb' ][ 'mutasi-proyek' ] = $categoryItemsATB->where ( 'tipe', 'mutasi-proyek' )->sum ( function ($item)
            {
                return $item->quantity * $item->harga;
            } );

            $this->sums[ $category[ 'kode' ] ][ 'atb' ][ 'panjar-proyek' ] = $categoryItemsATB->where ( 'tipe', 'panjar-proyek' )->sum ( function ($item)
            {
                return $item->quantity * $item->harga;
            } );

            // Calculate total ATB
            $this->sums[ $category[ 'kode' ] ][ 'atb' ][ 'total' ] =
                $this->sums[ $category[ 'kode' ] ][ 'atb' ][ 'hutang-unit-alat' ] +
                $this->sums[ $category[ 'kode' ] ][ 'atb' ][ 'panjar-unit-alat' ] +
                $this->sums[ $category[ 'kode' ] ][ 'atb' ][ 'mutasi-proyek' ] +
                $this->sums[ $category[ 'kode' ] ][ 'atb' ][ 'panjar-proyek' ];

            // Calculate APB values by tipe (using the same approach as in LaporanLNPBBulanBerjalanController)
            $this->sums[ $category[ 'kode' ] ][ 'apb' ][ 'hutang-unit-alat' ] = $categoryItemsAPB->where ( 'tipe', 'hutang-unit-alat' )->whereNotIn ( 'status', [ 'pending', 'rejected' ] )->sum ( function ($item)
            {
                return $item->quantity * ( $item->saldo ? $item->saldo->harga : 0 );
            } );

            $this->sums[ $category[ 'kode' ] ][ 'apb' ][ 'panjar-unit-alat' ] = $categoryItemsAPB->where ( 'tipe', 'panjar-unit-alat' )->whereNotIn ( 'status', [ 'pending', 'rejected' ] )->sum ( function ($item)
            {
                return $item->quantity * ( $item->saldo ? $item->saldo->harga : 0 );
            } );

            $this->sums[ $category[ 'kode' ] ][ 'apb' ][ 'mutasi-proyek' ] = $categoryItemsAPB->where ( 'tipe', 'mutasi-proyek' )->whereNotIn ( 'status', [ 'pending', 'rejected' ] )->sum ( function ($item)
            {
                return $item->quantity * ( $item->saldo ? $item->saldo->harga : 0 );
            } );

            $this->sums[ $category[ 'kode' ] ][ 'apb' ][ 'panjar-proyek' ] = $categoryItemsAPB->where ( 'tipe', 'panjar-proyek' )->whereNotIn ( 'status', [ 'pending', 'rejected' ] )->sum ( function ($item)
            {
                return $item->quantity * ( $item->saldo ? $item->saldo->harga : 0 );
            } );

            // Calculate total APB
            $this->sums[ $category[ 'kode' ] ][ 'apb' ][ 'total' ] =
                $this->sums[ $category[ 'kode' ] ][ 'apb' ][ 'hutang-unit-alat' ] +
                $this->sums[ $category[ 'kode' ] ][ 'apb' ][ 'panjar-unit-alat' ] +
                $this->sums[ $category[ 'kode' ] ][ 'apb' ][ 'mutasi-proyek' ] +
                $this->sums[ $category[ 'kode' ] ][ 'apb' ][ 'panjar-proyek' ];

            // Calculate Saldo values (ATB - APB)
            $this->sums[ $category[ 'kode' ] ][ 'saldo' ][ 'hutang-unit-alat' ] =
                $this->sums[ $category[ 'kode' ] ][ 'atb' ][ 'hutang-unit-alat' ] -
                $this->sums[ $category[ 'kode' ] ][ 'apb' ][ 'hutang-unit-alat' ];

            $this->sums[ $category[ 'kode' ] ][ 'saldo' ][ 'panjar-unit-alat' ] =
                $this->sums[ $category[ 'kode' ] ][ 'atb' ][ 'panjar-unit-alat' ] -
                $this->sums[ $category[ 'kode' ] ][ 'apb' ][ 'panjar-unit-alat' ];

            $this->sums[ $category[ 'kode' ] ][ 'saldo' ][ 'mutasi-proyek' ] =
                $this->sums[ $category[ 'kode' ] ][ 'atb' ][ 'mutasi-proyek' ] -
                $this->sums[ $category[ 'kode' ] ][ 'apb' ][ 'mutasi-proyek' ];

            $this->sums[ $category[ 'kode' ] ][ 'saldo' ][ 'panjar-proyek' ] =
                $this->sums[ $category[ 'kode' ] ][ 'atb' ][ 'panjar-proyek' ] -
                $this->sums[ $category[ 'kode' ] ][ 'apb' ][ 'panjar-proyek' ];

            $this->sums[ $category[ 'kode' ] ][ 'saldo' ][ 'total' ] =
                $this->sums[ $category[ 'kode' ] ][ 'atb' ][ 'total' ] -
                $this->sums[ $category[ 'kode' ] ][ 'apb' ][ 'total' ];
        }
    }

    public function collection ()
    {
        // Return the data array as a collection
        return collect ( $this->prepareData () );
    }

    public function startCell () : string
    {
        return 'B2';
    }

    public function headings () : array
    {
        $proyek = $this->proyekId ? Proyek::find ( $this->proyekId ) : null;
        $title  = $proyek ? "LNPB BULAN BERJALAN - {$proyek->nama}" : "LNPB BULAN BERJALAN - SEMUA PROYEK";

        return [ 
            [ $title ],
            [ '' ],
            [ 'Periode', ':', Carbon::parse ( $this->startDate )->format ( 'd F Y' ) . ' s/d ' . Carbon::parse ( $this->endDate )->format ( 'd F Y' ) ],
            [ '' ],
            [ 
                'NO.',
                'URAIAN',
                'PENERIMAAN (RP)',
                '',
                '',
                '',
                "TOTAL\nPENERIMAAN\n(RP)",
                'PENGELUARAN (RP)',
                '',
                '',
                '',
                "TOTAL\nPENGELUARAN\n(RP)",
                'SALDO (RP)',
                '',
                '',
                '',
                "TOTAL SALDO\n(RP)"
            ],
            [ 
                '',
                '',
                'HUTANG UNIT ALAT',
                'PANJAR UNIT ALAT',
                'MUTASI PROYEK',
                'PANJAR PROYEK',
                '',
                'HUTANG UNIT ALAT',
                'PANJAR UNIT ALAT',
                'MUTASI PROYEK',
                'PANJAR PROYEK',
                '',
                'HUTANG UNIT ALAT',
                'PANJAR UNIT ALAT',
                'MUTASI PROYEK',
                'PANJAR PROYEK',
                ''
            ]
        ];
    }

    public function map ( $row ) : array
    {
        // Function to format values
        $formatValue = function ($value)
        {
            return $value;
        };

        if ( $row[ 'kode' ] === 'section_header' )
        {
            return [ 
                $row[ 'number' ],
                $row[ 'nama' ],
                $formatValue ( $row[ 'atb_hutang' ] ),
                $formatValue ( $row[ 'atb_panjar' ] ),
                $formatValue ( $row[ 'atb_mutasi' ] ),
                $formatValue ( $row[ 'atb_panjar_proyek' ] ),
                $formatValue ( $row[ 'atb_total' ] ),
                $formatValue ( $row[ 'apb_hutang' ] ),
                $formatValue ( $row[ 'apb_panjar' ] ),
                $formatValue ( $row[ 'apb_mutasi' ] ),
                $formatValue ( $row[ 'apb_panjar_proyek' ] ),
                $formatValue ( $row[ 'apb_total' ] ),
                $formatValue ( $row[ 'saldo_hutang' ] ),
                $formatValue ( $row[ 'saldo_panjar' ] ),
                $formatValue ( $row[ 'saldo_mutasi' ] ),
                $formatValue ( $row[ 'saldo_panjar_proyek' ] ),
                $formatValue ( $row[ 'saldo_total' ] )
            ];
        }
        // For total row
        elseif ( $row[ 'kode' ] === 'total_row' )
        {
            return [ 
                '',
                $row[ 'nama' ],
                $formatValue ( $row[ 'atb_hutang' ] ),
                $formatValue ( $row[ 'atb_panjar' ] ),
                $formatValue ( $row[ 'atb_mutasi' ] ),
                $formatValue ( $row[ 'atb_panjar_proyek' ] ),
                $formatValue ( $row[ 'atb_total' ] ),
                $formatValue ( $row[ 'apb_hutang' ] ),
                $formatValue ( $row[ 'apb_panjar' ] ),
                $formatValue ( $row[ 'apb_mutasi' ] ),
                $formatValue ( $row[ 'apb_panjar_proyek' ] ),
                $formatValue ( $row[ 'apb_total' ] ),
                $formatValue ( $row[ 'saldo_hutang' ] ),
                $formatValue ( $row[ 'saldo_panjar' ] ),
                $formatValue ( $row[ 'saldo_mutasi' ] ),
                $formatValue ( $row[ 'saldo_panjar_proyek' ] ),
                $formatValue ( $row[ 'saldo_total' ] )
            ];
        }

        // For regular items
        $sumData = $this->sums[ $row[ 'kode' ] ] ?? null;

        if ( ! $sumData )
        {
            return array_fill ( 0, 17, 0 );
        }

        return [ 
            $row[ 'number' ],
            $row[ 'nama' ],
            $formatValue ( $sumData[ 'atb' ][ 'hutang-unit-alat' ] ),
            $formatValue ( $sumData[ 'atb' ][ 'panjar-unit-alat' ] ),
            $formatValue ( $sumData[ 'atb' ][ 'mutasi-proyek' ] ),
            $formatValue ( $sumData[ 'atb' ][ 'panjar-proyek' ] ),
            $formatValue ( $sumData[ 'atb' ][ 'total' ] ),
            $formatValue ( $sumData[ 'apb' ][ 'hutang-unit-alat' ] ),
            $formatValue ( $sumData[ 'apb' ][ 'panjar-unit-alat' ] ),
            $formatValue ( $sumData[ 'apb' ][ 'mutasi-proyek' ] ),
            $formatValue ( $sumData[ 'apb' ][ 'panjar-proyek' ] ),
            $formatValue ( $sumData[ 'apb' ][ 'total' ] ),
            $formatValue ( $sumData[ 'saldo' ][ 'hutang-unit-alat' ] ),
            $formatValue ( $sumData[ 'saldo' ][ 'panjar-unit-alat' ] ),
            $formatValue ( $sumData[ 'saldo' ][ 'mutasi-proyek' ] ),
            $formatValue ( $sumData[ 'saldo' ][ 'panjar-proyek' ] ),
            $formatValue ( $sumData[ 'saldo' ][ 'total' ] )
        ];
    }

    public function styles ( Worksheet $sheet )
    {
        $lastRow    = $sheet->getHighestRow ();
        $lastColumn = 'R'; // The last column is R (TOTAL SALDO)

        // Style for title
        $sheet->mergeCells ( 'B2:R2' );
        $sheet->getStyle ( 'B2' )->applyFromArray ( [ 
            'font'      => [ 
                'bold' => true,
                'size' => 14,
            ],
            'alignment' => [ 
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ] );

        // Style for period - Fix the period display with colon
        $sheet->mergeCells ( 'D4:R4' ); // Merge cells after the colon
        $sheet->getStyle ( 'B4' )->getFont ()->setBold ( true ); // Make 'Periode' label bold
        $sheet->getStyle ( 'C4' )->getAlignment ()->setHorizontal (
            \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
        ); // Center the colon
        $sheet->getStyle ( 'D4' )->getAlignment ()->setHorizontal (
            \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT
        ); // Left-align the date range

        // Apply thin borders and alignment to the entire table
        $sheet->getStyle ( "B6:{$lastColumn}{$lastRow}" )->applyFromArray ( [ 
            'borders'   => [ 
                'allBorders' => [ 
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
                // Remove medium outline border
            ],
            'alignment' => [ 
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ] );

        // Style for headers
        $sheet->mergeCells ( 'B6:B7' );  // NO.
        $sheet->mergeCells ( 'C6:C7' );  // URAIAN
        $sheet->mergeCells ( 'D6:G6' );  // PENERIMAAN
        $sheet->mergeCells ( 'I6:L6' );  // PENGELUARAN  
        $sheet->mergeCells ( 'N6:Q6' );  // SALDO

        $sheet->mergeCells ( 'H6:H7' );  // TOTAL PENERIMAAN (RP)
        $sheet->mergeCells ( 'M6:M7' );  // TOTAL PENGELUARAN (RP)
        $sheet->mergeCells ( 'R6:R7' );  // TOTAL SALDO (RP)

        // Center align all header cells and enable word wrap for multi-line headers
        $sheet->getStyle ( 'B6:R7' )->applyFromArray ( [ 
            'alignment' => [ 
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'wrapText'   => true,
            ],
            'font'      => [ 
                'bold' => true,
            ],
            'fill'      => [ 
                'fillType'   => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [ 
                    'rgb' => 'c0c0c0', // Light gray background for headers
                ],
            ],
        ] );

        // Add colors for sections
        $sheet->getStyle ( "D8:G$lastRow" )->getFill ()
            ->setFillType ( \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID )
            ->getStartColor ()->setRGB ( 'e6ffe6' ); // Light green for penerimaan

        $sheet->getStyle ( "I8:L$lastRow" )->getFill ()
            ->setFillType ( \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID )
            ->getStartColor ()->setRGB ( 'fff2e6' ); // Light orange for pengeluaran

        $sheet->getStyle ( "N8:Q$lastRow" )->getFill ()
            ->setFillType ( \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID )
            ->getStartColor ()->setRGB ( 'e6f3ff' ); // Light blue for saldo

        // Format currency columns
        foreach ( range ( 'D', 'R' ) as $col )
        {
            $sheet->getStyle ( "{$col}8:{$col}{$lastRow}" )
                ->getNumberFormat ()
                ->setFormatCode ( '#,##0.00_-;(#,##0.00)' );

            // Right-align currency columns
            $sheet->getStyle ( "{$col}8:{$col}{$lastRow}" )
                ->getAlignment ()
                ->setHorizontal ( \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT );
        }

        // Auto-adjust column widths
        foreach ( range ( 'B', $lastColumn ) as $column )
        {
            $sheet->getColumnDimension ( $column )->setAutoSize ( true );
        }

        // Center "NO." column
        $sheet->getStyle ( "B8:B{$lastRow}" )
            ->getAlignment ()
            ->setHorizontal ( \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER );

        // Left-align URAIAN column and disable word wrap for columns B and C
        $sheet->getStyle ( "B8:B{$lastRow}" )
            ->getAlignment ()
            ->setHorizontal ( \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT );

        $sheet->getStyle ( "C8:C{$lastRow}" )
            ->getAlignment ()
            ->setHorizontal ( \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT );

        // Bold section headers and TYRE row
        foreach ( $this->collection () as $index => $row )
        {
            $rowNumber = $index + 8;
            if ( $row[ 'kode' ] === 'section_header' || $row[ 'kode' ] === 'total_row' || $row[ 'kode' ] === 'B3' )
            {
                $sheet->getStyle ( "B{$rowNumber}:R{$rowNumber}" )
                    ->getFont ()
                    ->setBold ( true );
            }
        }

        // Style for total row - bold and different background but with thin borders
        $lastRow = $sheet->getHighestRow ();
        $sheet->mergeCells ( "B{$lastRow}:C{$lastRow}" ); // Merge columns B and C for the total row
        $sheet->setCellValue ( "B{$lastRow}", "TOTAL" ); // Set the TOTAL text
        $sheet->getStyle ( "B{$lastRow}:C{$lastRow}" )->applyFromArray ( [ 
            'alignment' => [ 
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ] );
        $sheet->getStyle ( "B{$lastRow}:R{$lastRow}" )->applyFromArray ( [ 
            'font'    => [ 
                'bold' => true,
            ],
            'borders' => [ 
                'allBorders' => [ 
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
                'top'        => [ 
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                ],
            ],
        ] );

        // Set appropriate column widths
        $sheet->getColumnDimension ( 'B' )->setWidth ( 5 ); // NO.
        $sheet->getColumnDimension ( 'C' )->setWidth ( 25 ); // URAIAN
        foreach ( range ( 'D', 'R' ) as $col )
        {
            $sheet->getColumnDimension ( $col )->setWidth ( 15 ); // All numeric columns
        }

        // Set up grouping levels (added from LNPBTotalExport.php)
        $currentRow = 8;
        $collection = $this->collection ();

        foreach ( $collection as $row )
        {
            // Skip applying outline level for TOTAL row
            if ( $row[ 'kode' ] === 'total_row' )
            {
                $currentRow++;
                continue;
            }

            $name   = $row[ 'nama' ];
            $number = $row[ 'number' ] ?? '';

            if ( $row[ 'kode' ] === 'section_header' )
            {
                $level = match ( $name )
                {
                    'SUKU CADANG', 'MATERIAL' => 1,
                    'PERBAIKAN', 'PEMELIHARAAN' => 2,
                    'Maintenance Kit', 'Oil & Lubricants', 'TYRE' => 3,
                    default => 4
                };

                // Set outline level but keep all rows visible
                if ( $level > 1 )
                {
                    $sheet->getRowDimension ( $currentRow )->setOutlineLevel ( $level - 1 );
                }
            }
            else
            {
                // For regular items
                $level = match ( true )
                {
                    str_starts_with ( $number, 'A.' ) => 3, // Perbaikan items
                    str_starts_with ( $number, 'B.1.' ) => 4, // Maintenance Kit items
                    str_starts_with ( $number, 'B.2.' ) => 4, // Oil & Lubricants items
                    str_starts_with ( $number, 'B.3' ) => 3, // TYRE as a single item
                    str_starts_with ( $number, 'C.' ) => 2, // Workshop
                    default => 4
                };

                $sheet->getRowDimension ( $currentRow )->setOutlineLevel ( $level - 1 );
            }

            $currentRow++;
        }

        // Set outline properties
        $sheet->setShowSummaryBelow ( false );
        $sheet->setShowSummaryRight ( false );

        return [];
    }

    public function registerEvents () : array
    {
        return [ 
            AfterSheet::class => function (AfterSheet $event)
            {
                $event->sheet->getDelegate ()->setSelectedCell ( 'A1' );
            },
        ];
    }

    private function prepareData ()
    {
        $sections = [ 
            [ 
                'kode'        => 'I',
                'nama'        => 'SUKU CADANG',
                'number'      => 'I',
                'subsections' => [ 
                    [ 
                        'kode'   => 'A',
                        'nama'   => 'PERBAIKAN',
                        'number' => 'A',
                        'items'  => array_map ( function ($i)
                        {
                            return [ 
                                'code'   => "A$i",
                                'number' => "A.$i"
                            ];
                        }, range ( 1, 14 ) )
                    ],
                    [ 
                        'kode'        => 'B',
                        'nama'        => 'PEMELIHARAAN',
                        'number'      => 'B',
                        'subsections' => [ 
                            [ 
                                'kode'   => 'B.1',
                                'nama'   => 'Maintenance Kit',
                                'number' => 'B.1',
                                'items'  => array_map ( function ($i)
                                {
                                    return [ 
                                        'code'   => "B1$i",
                                        'number' => "B.1.$i"
                                    ];
                                }, range ( 1, 6 ) )
                            ],
                            [ 
                                'kode'   => 'B.2',
                                'nama'   => 'Oil & Lubricants',
                                'number' => 'B.2',
                                'items'  => array_map ( function ($i)
                                {
                                    return [ 
                                        'code'   => "B2$i",
                                        'number' => "B.2.$i"
                                    ];
                                }, range ( 1, 9 ) )
                            ]
                        ],
                        'items'       => [ 
                            [ 
                                'code'   => 'B3',
                                'number' => 'B.3'
                            ]
                        ]
                    ]
                ]
            ],
            [ 
                'kode'   => 'II',
                'nama'   => 'MATERIAL',
                'number' => 'II',
                'items'  => [ 
                    [ 'code' => 'C1', 'number' => 'C.1' ]
                ]
            ]
        ];

        $rows = [];
        foreach ( $sections as $section )
        {
            $sectionTotals = $this->calculateSectionTotals ( $section );

            // Add section header (I or II)
            $rows[] = [ 
                'kode'   => 'section_header',
                'nama'   => $section[ 'nama' ],
                'level'  => 0,
                'number' => $section[ 'number' ]
            ] + $sectionTotals;

            if ( isset ( $section[ 'subsections' ] ) )
            {
                foreach ( $section[ 'subsections' ] as $subsection )
                {
                    $subsectionTotals = $this->calculateSectionTotals ( $subsection );

                    // Add subsection header (A, B)
                    $rows[] = [ 
                        'kode'   => 'section_header',
                        'nama'   => $subsection[ 'nama' ],
                        'level'  => 1,
                        'number' => $subsection[ 'number' ]
                    ] + $subsectionTotals;

                    // Process sub-subsections FIRST (key change)
                    if ( isset ( $subsection[ 'subsections' ] ) )
                    {
                        foreach ( $subsection[ 'subsections' ] as $subsubsection )
                        {
                            $subsubsectionTotals = $this->calculateSectionTotals ( $subsubsection );

                            // Add sub-subsection header (B.1, B.2)
                            $rows[] = [ 
                                'kode'   => 'section_header',
                                'nama'   => $subsubsection[ 'nama' ],
                                'level'  => 2,
                                'number' => $subsubsection[ 'number' ]
                            ] + $subsubsectionTotals;

                            // Process items in this sub-subsection
                            if ( isset ( $subsubsection[ 'items' ] ) )
                            {
                                foreach ( $subsubsection[ 'items' ] as $item )
                                {
                                    $itemData = $this->data[ array_search ( $item[ 'code' ], array_column ( $this->data, 'kode' ) ) ];
                                    $rows[]   = [ 
                                        'kode'   => $item[ 'code' ],
                                        'level'  => 3,
                                        'number' => $item[ 'number' ]
                                    ] + $itemData;
                                }
                            }
                        }
                    }

                    // THEN process direct items under subsection (like TYRE) - this is the key change
                    if ( isset ( $subsection[ 'items' ] ) )
                    {
                        foreach ( $subsection[ 'items' ] as $item )
                        {
                            $itemData = $this->data[ array_search ( $item[ 'code' ], array_column ( $this->data, 'kode' ) ) ];
                            $rows[]   = [ 
                                'kode'   => $item[ 'code' ],
                                'level'  => 2,
                                'number' => $item[ 'number' ]
                            ] + $itemData;
                        }
                    }
                }
            }

            // Add direct items under section if any
            if ( isset ( $section[ 'items' ] ) )
            {
                foreach ( $section[ 'items' ] as $item )
                {
                    $itemData = $this->data[ array_search ( $item[ 'code' ], array_column ( $this->data, 'kode' ) ) ];
                    $rows[]   = [ 
                        'kode'   => $item[ 'code' ],
                        'level'  => 1,
                        'number' => $item[ 'number' ]
                    ] + $itemData;
                }
            }
        }

        // Calculate grand totals
        $grand_total_atb_hutang        = 0;
        $grand_total_atb_panjar        = 0;
        $grand_total_atb_mutasi        = 0;
        $grand_total_atb_panjar_proyek = 0;
        $grand_total_atb_total         = 0;

        $grand_total_apb_hutang        = 0;
        $grand_total_apb_panjar        = 0;
        $grand_total_apb_mutasi        = 0;
        $grand_total_apb_panjar_proyek = 0;
        $grand_total_apb_total         = 0;

        $grand_total_saldo_hutang        = 0;
        $grand_total_saldo_panjar        = 0;
        $grand_total_saldo_mutasi        = 0;
        $grand_total_saldo_panjar_proyek = 0;
        $grand_total_saldo_total         = 0;

        foreach ( $this->sums as $category )
        {
            $grand_total_atb_hutang += $category[ 'atb' ][ 'hutang-unit-alat' ];
            $grand_total_atb_panjar += $category[ 'atb' ][ 'panjar-unit-alat' ];
            $grand_total_atb_mutasi += $category[ 'atb' ][ 'mutasi-proyek' ];
            $grand_total_atb_panjar_proyek += $category[ 'atb' ][ 'panjar-proyek' ];
            $grand_total_atb_total += $category[ 'atb' ][ 'total' ];

            $grand_total_apb_hutang += $category[ 'apb' ][ 'hutang-unit-alat' ];
            $grand_total_apb_panjar += $category[ 'apb' ][ 'panjar-unit-alat' ];
            $grand_total_apb_mutasi += $category[ 'apb' ][ 'mutasi-proyek' ];
            $grand_total_apb_panjar_proyek += $category[ 'apb' ][ 'panjar-proyek' ];
            $grand_total_apb_total += $category[ 'apb' ][ 'total' ];

            $grand_total_saldo_hutang += $category[ 'saldo' ][ 'hutang-unit-alat' ];
            $grand_total_saldo_panjar += $category[ 'saldo' ][ 'panjar-unit-alat' ];
            $grand_total_saldo_mutasi += $category[ 'saldo' ][ 'mutasi-proyek' ];
            $grand_total_saldo_panjar_proyek += $category[ 'saldo' ][ 'panjar-proyek' ];
            $grand_total_saldo_total += $category[ 'saldo' ][ 'total' ];
        }

        // Add grand total row
        $rows[] = [ 
            'kode'                => 'total_row',
            'nama'                => 'TOTAL',
            'level'               => 0,
            'atb_hutang'          => $grand_total_atb_hutang,
            'atb_panjar'          => $grand_total_atb_panjar,
            'atb_mutasi'          => $grand_total_atb_mutasi,
            'atb_panjar_proyek'   => $grand_total_atb_panjar_proyek,
            'atb_total'           => $grand_total_atb_total,
            'apb_hutang'          => $grand_total_apb_hutang,
            'apb_panjar'          => $grand_total_apb_panjar,
            'apb_mutasi'          => $grand_total_apb_mutasi,
            'apb_panjar_proyek'   => $grand_total_apb_panjar_proyek,
            'apb_total'           => $grand_total_apb_total,
            'saldo_hutang'        => $grand_total_saldo_hutang,
            'saldo_panjar'        => $grand_total_saldo_panjar,
            'saldo_mutasi'        => $grand_total_saldo_mutasi,
            'saldo_panjar_proyek' => $grand_total_saldo_panjar_proyek,
            'saldo_total'         => $grand_total_saldo_total
        ];

        return $rows;
    }

    private function calculateSectionTotals ( $section )
    {
        $totals = [ 
            'atb_hutang'          => 0,
            'atb_panjar'          => 0,
            'atb_mutasi'          => 0,
            'atb_panjar_proyek'   => 0,
            'atb_total'           => 0,
            'apb_hutang'          => 0,
            'apb_panjar'          => 0,
            'apb_mutasi'          => 0,
            'apb_panjar_proyek'   => 0,
            'apb_total'           => 0,
            'saldo_hutang'        => 0,
            'saldo_panjar'        => 0,
            'saldo_mutasi'        => 0,
            'saldo_panjar_proyek' => 0,
            'saldo_total'         => 0
        ];

        if ( isset ( $section[ 'items' ] ) )
        {
            foreach ( $section[ 'items' ] as $item )
            {
                $sumData = $this->sums[ $item[ 'code' ] ] ?? [ 
                    'atb'   => [ 
                        'hutang-unit-alat' => 0,
                        'panjar-unit-alat' => 0,
                        'mutasi-proyek'    => 0,
                        'panjar-proyek'    => 0,
                        'total'            => 0
                    ],
                    'apb'   => [ 
                        'hutang-unit-alat' => 0,
                        'panjar-unit-alat' => 0,
                        'mutasi-proyek'    => 0,
                        'panjar-proyek'    => 0,
                        'total'            => 0
                    ],
                    'saldo' => [ 
                        'hutang-unit-alat' => 0,
                        'panjar-unit-alat' => 0,
                        'mutasi-proyek'    => 0,
                        'panjar-proyek'    => 0,
                        'total'            => 0
                    ]
                ];

                $totals[ 'atb_hutang' ] += $sumData[ 'atb' ][ 'hutang-unit-alat' ];
                $totals[ 'atb_panjar' ] += $sumData[ 'atb' ][ 'panjar-unit-alat' ];
                $totals[ 'atb_mutasi' ] += $sumData[ 'atb' ][ 'mutasi-proyek' ];
                $totals[ 'atb_panjar_proyek' ] += $sumData[ 'atb' ][ 'panjar-proyek' ];
                $totals[ 'atb_total' ] += $sumData[ 'atb' ][ 'total' ];

                $totals[ 'apb_hutang' ] += $sumData[ 'apb' ][ 'hutang-unit-alat' ];
                $totals[ 'apb_panjar' ] += $sumData[ 'apb' ][ 'panjar-unit-alat' ];
                $totals[ 'apb_mutasi' ] += $sumData[ 'apb' ][ 'mutasi-proyek' ];
                $totals[ 'apb_panjar_proyek' ] += $sumData[ 'apb' ][ 'panjar-proyek' ];
                $totals[ 'apb_total' ] += $sumData[ 'apb' ][ 'total' ];

                $totals[ 'saldo_hutang' ] += $sumData[ 'saldo' ][ 'hutang-unit-alat' ];
                $totals[ 'saldo_panjar' ] += $sumData[ 'saldo' ][ 'panjar-unit-alat' ];
                $totals[ 'saldo_mutasi' ] += $sumData[ 'saldo' ][ 'mutasi-proyek' ];
                $totals[ 'saldo_panjar_proyek' ] += $sumData[ 'saldo' ][ 'panjar-proyek' ];
                $totals[ 'saldo_total' ] += $sumData[ 'saldo' ][ 'total' ];
            }
        }

        if ( isset ( $section[ 'subsections' ] ) )
        {
            foreach ( $section[ 'subsections' ] as $subsection )
            {
                $subsectionTotals = $this->calculateSectionTotals ( $subsection );

                $totals[ 'atb_hutang' ] += $subsectionTotals[ 'atb_hutang' ];
                $totals[ 'atb_panjar' ] += $subsectionTotals[ 'atb_panjar' ];
                $totals[ 'atb_mutasi' ] += $subsectionTotals[ 'atb_mutasi' ];
                $totals[ 'atb_panjar_proyek' ] += $subsectionTotals[ 'atb_panjar_proyek' ];
                $totals[ 'atb_total' ] += $subsectionTotals[ 'atb_total' ];

                $totals[ 'apb_hutang' ] += $subsectionTotals[ 'apb_hutang' ];
                $totals[ 'apb_panjar' ] += $subsectionTotals[ 'apb_panjar' ];
                $totals[ 'apb_mutasi' ] += $subsectionTotals[ 'apb_mutasi' ];
                $totals[ 'apb_panjar_proyek' ] += $subsectionTotals[ 'apb_panjar_proyek' ];
                $totals[ 'apb_total' ] += $subsectionTotals[ 'apb_total' ];

                $totals[ 'saldo_hutang' ] += $subsectionTotals[ 'saldo_hutang' ];
                $totals[ 'saldo_panjar' ] += $subsectionTotals[ 'saldo_panjar' ];
                $totals[ 'saldo_mutasi' ] += $subsectionTotals[ 'saldo_mutasi' ];
                $totals[ 'saldo_panjar_proyek' ] += $subsectionTotals[ 'saldo_panjar_proyek' ];
                $totals[ 'saldo_total' ] += $subsectionTotals[ 'saldo_total' ];
            }
        }

        return $totals;
    }
}