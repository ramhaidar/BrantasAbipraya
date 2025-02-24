<?php

namespace App\Exports;

use App\Models\ATB;
use App\Models\APB;
use App\Models\Saldo;
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

class LNPBTotalExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithCustomStartCell, WithEvents, WithStrictNullComparison
{
    protected $proyekId;
    protected $startDate;
    protected $endDate;
    protected $data;
    protected $sums_before;
    protected $sums_current;

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
        // Query for current period
        $ATB_Current = ATB::with ( 'masterDataSparepart.KategoriSparepart' )
            ->when ( $this->proyekId, function ($query)
            {
                return $query->where ( 'id_proyek', $this->proyekId );
            } )
            ->whereBetween ( 'tanggal', [ $this->startDate, $this->endDate ] )
            ->get ();

        $APB_Current = APB::with ( 'masterDataSparepart.KategoriSparepart' )
            ->when ( $this->proyekId, function ($query)
            {
                return $query->where ( 'id_proyek', $this->proyekId );
            } )
            ->whereBetween ( 'tanggal', [ $this->startDate, $this->endDate ] )
            ->get ();

        // Query for previous period
        $ATB_Before = ATB::with ( 'masterDataSparepart.KategoriSparepart' )
            ->when ( $this->proyekId, function ($query)
            {
                return $query->where ( 'id_proyek', $this->proyekId );
            } )
            ->where ( 'tanggal', '<', $this->startDate )
            ->get ();

        $APB_Before = APB::with ( 'masterDataSparepart.KategoriSparepart' )
            ->when ( $this->proyekId, function ($query)
            {
                return $query->where ( 'id_proyek', $this->proyekId );
            } )
            ->where ( 'tanggal', '<', $this->startDate )
            ->get ();

        // Calculate sums for each period
        $this->sums_current = [];
        $this->sums_before  = [];

        foreach ( $this->data as $category )
        {
            $this->sums_current[ $category[ 'kode' ] ] = $this->calculateCategorySums ( $category, $ATB_Current, $APB_Current );
            $this->sums_before[ $category[ 'kode' ] ]  = $this->calculateCategorySums ( $category, $ATB_Before, $APB_Before );
        }
    }

    private function calculateCategorySums ( $category, $ATB, $APB )
    {
        $categoryItemsATB = $ATB->filter ( function ($item) use ($category)
        {
            return $item->masterDataSparepart->kategoriSparepart->kode === $category[ 'kode' ];
        } );

        $categoryItemsAPB = $APB->filter ( function ($item) use ($category)
        {
            return $item->masterDataSparepart->kategoriSparepart->kode === $category[ 'kode' ];
        } );

        $atb = $categoryItemsATB->sum ( function ($item)
        {
            return $item->quantity * $item->harga;
        } );

        $apb = $categoryItemsAPB->whereNotIn ( 'status', [ 'pending', 'rejected' ] )->sum ( function ($item)
        {
            return $item->quantity * $item->saldo->harga;
        } );

        return [ 
            'nama'     => $category[ 'nama' ],
            'jenis'    => $category[ 'jenis' ],
            'subJenis' => $category[ 'subJenis' ],
            'atb'      => $atb,
            'apb'      => $apb,
            'saldo'    => $atb - $apb
        ];
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
        $title  = $proyek ? "LNPB TOTAL - {$proyek->nama}" : "LNPB TOTAL - SEMUA PROYEK";

        return [ 
            [ $title ],
            [ '' ],
            [ 'Periode', ':', Carbon::parse ( $this->startDate )->format ( 'd F Y' ) . ' s/d ' . Carbon::parse ( $this->endDate )->format ( 'd F Y' ) ],
            [ '' ],
            [ 
                'NO.',
                'URAIAN',
                'S/D BULAN LALU (Rp)',
                '',
                '',
                'BULAN INI (Rp)',
                '',
                '',
                'S/D BULAN INI (Rp)',
                '',
                ''
            ],
            [ 
                '',
                '',
                'PENERIMAAN',
                'PENGELUARAN',
                'SALDO AKHIR',
                'PENERIMAAN',
                'PENGELUARAN',
                'SALDO AKHIR',
                'PENERIMAAN',
                'PENGELUARAN',
                'SALDO AKHIR'
            ],
        ];
    }

    public function map ( $row ) : array
    {
        $before  = $this->sums_before[ $row[ 'kode' ] ] ?? [ 'atb' => 0, 'apb' => 0, 'saldo' => 0 ];
        $current = $this->sums_current[ $row[ 'kode' ] ] ?? [ 'atb' => 0, 'apb' => 0, 'saldo' => 0 ];

        // Add text styles for section headers
        if ( $row[ 'kode' ] === 'section_header' )
        {
            return [ 
                $row[ 'number' ],
                $row[ 'nama' ], // No HTML tags needed
                $row[ 'before_atb' ],
                $row[ 'before_apb' ],
                $row[ 'before_saldo' ],
                $row[ 'current_atb' ],
                $row[ 'current_apb' ],
                $row[ 'current_saldo' ],
                $row[ 'before_atb' ] + $row[ 'current_atb' ],
                $row[ 'before_apb' ] + $row[ 'current_apb' ],
                $row[ 'before_saldo' ] + $row[ 'current_saldo' ]
            ];
        }

        // Regular items remain unchanged
        return [ 
            $row[ 'number' ],
            $row[ 'nama' ],
            $before[ 'atb' ],
            $before[ 'apb' ],
            $before[ 'saldo' ],
            $current[ 'atb' ],
            $current[ 'apb' ],
            $current[ 'saldo' ],
            $before[ 'atb' ] + $current[ 'atb' ],
            $before[ 'apb' ] + $current[ 'apb' ],
            $before[ 'saldo' ] + $current[ 'saldo' ]
        ];
    }

    private function prepareData ()
    {
        $sections = [ 
            [ 
                'kode'        => 'I',
                'nama'        => 'SUKU CADANG',
                'number'      => 'I',  // No dot for main sections
                'subsections' => [ 
                    [ 
                        'kode'   => 'A',
                        'nama'   => 'PERBAIKAN',
                        'number' => 'A', // Remove dot
                        'items'  => array_map ( function ($i)
                        {
                            return [ 
                                'code'   => "A$i",
                                'number' => "A.$i" // Remove trailing dot
                            ];
                        }, range ( 1, 14 ) )
                    ],
                    [ 
                        'kode'        => 'B',
                        'nama'        => 'PEMELIHARAAN',
                        'number'      => 'B', // Remove dot
                        'subsections' => [ 
                            [ 
                                'kode'   => 'B.1',
                                'nama'   => 'Maintenance Kit',
                                'number' => 'B.1', // Remove dot
                                'items'  => array_map ( function ($i)
                                {
                                    return [ 
                                        'code'   => "B1$i",
                                        'number' => "B.1.$i" // Remove trailing dot
                                    ];
                                }, range ( 1, 6 ) )
                            ],
                            [ 
                                'kode'   => 'B.2',
                                'nama'   => 'Oil & Lubricants',
                                'number' => 'B.2', // Remove dot
                                'items'  => array_map ( function ($i)
                                {
                                    return [ 
                                        'code'   => "B2$i",
                                        'number' => "B.2.$i" // Remove trailing dot
                                    ];
                                }, range ( 1, 9 ) )
                            ]
                        ]
                    ],
                    // TYRE as direct subsection of PEMELIHARAAN with explicit level
                    [ 
                        'kode'   => 'B.3',
                        'nama'   => 'TYRE',
                        'number' => 'B.3',
                        'code'   => 'B3',
                        'level'  => 2  // Set explicit level for TYRE
                    ]
                ]
            ],
            [ 
                'kode'   => 'II',
                'nama'   => 'MATERIAL',
                'number' => 'II', // No dot for main sections
                'items'  => [ 
                    [ 'code' => 'C1', 'number' => 'C.1' ] // Remove trailing dot
                ]
            ]
        ];

        $rows = [];
        foreach ( $sections as $section )
        {
            $sectionTotals = $this->calculateSectionTotals ( $section );

            // Add section header (I or II, no dots)
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

                    // Add subsection header (A., B., etc)
                    $rows[] = [ 
                        'kode'   => 'section_header',
                        'nama'   => $subsection[ 'nama' ],
                        'level'  => 1,
                        'number' => $subsection[ 'number' ]
                    ] + $subsectionTotals;

                    if ( isset ( $subsection[ 'subsections' ] ) )
                    {
                        foreach ( $subsection[ 'subsections' ] as $subsubsection )
                        {
                            $subsubsectionTotals = $this->calculateSectionTotals ( $subsubsection );

                            // Add sub-subsection header (B.1., B.2., etc)
                            $rows[] = [ 
                                'kode'   => 'section_header',
                                'nama'   => $subsubsection[ 'nama' ],
                                'level'  => 2,
                                'number' => $subsubsection[ 'number' ]
                            ] + $subsubsectionTotals;

                            // Add items with proper numbering (B.1.1., B.1.2., etc)
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

                    // Add direct items under subsection if any
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

        return $rows;
    }

    private function calculateSectionTotals ( $section )
    {
        $before_atb    = 0;
        $before_apb    = 0;
        $before_saldo  = 0;
        $current_atb   = 0;
        $current_apb   = 0;
        $current_saldo = 0;

        $itemCodes = [];

        // Get item codes from direct items
        if ( isset ( $section[ 'items' ] ) )
        {
            foreach ( $section[ 'items' ] as $item )
            {
                $itemCodes[] = $item[ 'code' ];
            }
        }

        // Get item codes from subsections
        if ( isset ( $section[ 'subsections' ] ) )
        {
            foreach ( $section[ 'subsections' ] as $subsection )
            {
                // Get items from subsection
                if ( isset ( $subsection[ 'items' ] ) )
                {
                    foreach ( $subsection[ 'items' ] as $item )
                    {
                        $itemCodes[] = $item[ 'code' ];
                    }
                }

                // Get items from sub-subsections
                if ( isset ( $subsection[ 'subsections' ] ) )
                {
                    foreach ( $subsection[ 'subsections' ] as $subsubsection )
                    {
                        if ( isset ( $subsubsection[ 'items' ] ) )
                        {
                            foreach ( $subsubsection[ 'items' ] as $item )
                            {
                                $itemCodes[] = $item[ 'code' ];
                            }
                        }
                    }
                }
            }
        }

        foreach ( $itemCodes as $code )
        {
            if ( isset ( $this->sums_before[ $code ] ) )
            {
                $before_atb += $this->sums_before[ $code ][ 'atb' ];
                $before_apb += $this->sums_before[ $code ][ 'apb' ];
                $before_saldo += $this->sums_before[ $code ][ 'saldo' ];
            }
            if ( isset ( $this->sums_current[ $code ] ) )
            {
                $current_atb += $this->sums_current[ $code ][ 'atb' ];
                $current_apb += $this->sums_current[ $code ][ 'apb' ];
                $current_saldo += $this->sums_current[ $code ][ 'saldo' ];
            }
        }

        return [ 
            'before_atb'    => $before_atb,
            'before_apb'    => $before_apb,
            'before_saldo'  => $before_saldo,
            'current_atb'   => $current_atb,
            'current_apb'   => $current_apb,
            'current_saldo' => $current_saldo,
        ];
    }

    public function styles ( Worksheet $sheet )
    {
        $lastRow    = $sheet->getHighestRow ();
        $lastColumn = 'L';

        // Style for title
        $sheet->mergeCells ( 'B2:K2' );
        $sheet->getStyle ( 'B2' )->applyFromArray ( [ 
            'font'      => [ 
                'bold' => true,
                'size' => 14,
            ],
            'alignment' => [ 
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ] );

        // Style for period
        $sheet->getStyle ( 'B4' )->getFont ()->setBold ( true );
        $sheet->getStyle ( 'C4' )->getAlignment ()->setHorizontal ( \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER );

        // Style for headers
        $sheet->mergeCells ( 'B6:B7' );
        $sheet->mergeCells ( 'C6:C7' );
        $sheet->mergeCells ( 'D6:F6' );
        $sheet->mergeCells ( 'G6:I6' );
        $sheet->mergeCells ( 'J6:L6' );

        $headerStyle = [ 
            'font'      => [ 
                'bold'  => true,
                'color' => [ 'rgb' => '000000' ],
            ],
            'fill'      => [ 
                'fillType'   => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [ 'rgb' => 'c0c0c0' ],
            ],
            'alignment' => [ 
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'borders'   => [ 
                'allBorders' => [ 
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];

        $sheet->getStyle ( "B6:L7" )->applyFromArray ( $headerStyle );

        // Add background colors for different periods
        $sheet->getStyle ( "D8:F$lastRow" )->getFill ()->setFillType ( \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID )->getStartColor ()->setRGB ( 'e6ffe6' );
        $sheet->getStyle ( "G8:I$lastRow" )->getFill ()->setFillType ( \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID )->getStartColor ()->setRGB ( 'fff2e6' );
        $sheet->getStyle ( "J8:L$lastRow" )->getFill ()->setFillType ( \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID )->getStartColor ()->setRGB ( 'e6f3ff' );

        // Style for data cells
        $sheet->getStyle ( "B8:L$lastRow" )->applyFromArray ( [ 
            'borders' => [ 
                'allBorders' => [ 
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ] );

        // Format currency columns
        $currencyFormat = '#,##0';
        foreach ( range ( 'C', 'K' ) as $col )
        {
            $sheet->getStyle ( "{$col}7:{$col}{$lastRow}" )
                ->getNumberFormat ()
                ->setFormatCode ( $currencyFormat );
        }

        // Auto-adjust column widths
        foreach ( range ( 'B', $lastColumn ) as $column )
        {
            $sheet->getColumnDimension ( $column )->setAutoSize ( true );
        }

        // Enable rich text rendering
        $sheet->getStyle ( 'C6:C' . $sheet->getHighestRow () )
            ->getAlignment ()
            ->setWrapText ( true );

        // Make section headers and key rows bold by checking source data
        $collection = $this->collection ();
        $currentRow = 8; // Data starts from row 8

        foreach ( $collection as $row )
        {
            if (
                $row[ 'kode' ] === 'section_header' &&
                in_array ( $row[ 'nama' ], [ 
                    'SUKU CADANG',
                    'PERBAIKAN',
                    'PEMELIHARAAN',
                    'Maintenance Kit',
                    'Oil & Lubricants',
                    'TYRE',
                    'MATERIAL'
                ] )
            )
            {
                $sheet->getStyle ( "B{$currentRow}:L{$currentRow}" )
                    ->getFont ()
                    ->setBold ( true );
            }
            $currentRow++;
        }

        // Set up grouping levels and rows
        $currentRow  = 8;
        $collection  = $this->collection ();
        $groupStarts = [];

        foreach ( $collection as $row )
        {
            $name   = $row[ 'nama' ];
            $number = $row[ 'number' ] ?? '';

            if ( $row[ 'kode' ] === 'section_header' )
            {
                $level = match ( $name )
                {
                    'SUKU CADANG', 'MATERIAL' => 1,
                    'PERBAIKAN', 'PEMELIHARAAN' => 2,
                    'Maintenance Kit', 'Oil & Lubricants', 'TYRE' => 3, // TYRE at same level as other subsections
                    default => 4
                };

                // Set outline level
                if ( $level > 1 )
                {
                    $sheet->getRowDimension ( $currentRow )->setOutlineLevel ( $level - 1 );
                    $sheet->getRowDimension ( $currentRow )->setVisible ( false );
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
                $sheet->getRowDimension ( $currentRow )->setVisible ( false );
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
}
