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
        console ( "Starting calculateSums() with proyekId: " . ( $this->proyekId ?? 'null' ) );

        // === Calculate ATB, APB, and Saldo Current Period === //
        $ATB_Current = ATB::with ( 'masterDataSparepart.KategoriSparepart' )
            ->when ( $this->proyekId, function ($query)
            {
                return $query->where ( 'id_proyek', $this->proyekId );
            } )
            ->whereBetween ( 'tanggal', [ $this->startDate, $this->endDate ] )
            ->get ();

        console ( "ATB_Current count: " . $ATB_Current->count () );

        $APB_Current = APB::with ( 'masterDataSparepart.KategoriSparepart' )
            ->when ( $this->proyekId, function ($query)
            {
                return $query->where ( 'id_proyek', $this->proyekId );
            } )
            ->whereBetween ( 'tanggal', [ $this->startDate, $this->endDate ] )
            ->get ();

        console ( "APB_Current count: " . $APB_Current->count () );

        // +++ Calculate ATB, APB, and Saldo Before Current Period +++
        $ATB_Before = ATB::with ( 'masterDataSparepart.KategoriSparepart' )
            ->when ( $this->proyekId, function ($query)
            {
                return $query->where ( 'id_proyek', $this->proyekId );
            } )
            ->where ( 'tanggal', '<', $this->startDate )
            ->get ();

        console ( "ATB_Before count: " . $ATB_Before->count () );

        $APB_Before = APB::with ( 'masterDataSparepart.KategoriSparepart' )
            ->when ( $this->proyekId, function ($query)
            {
                return $query->where ( 'id_proyek', $this->proyekId );
            } )
            ->where ( 'tanggal', '<', $this->startDate )
            ->get ();

        console ( "APB_Before count: " . $APB_Before->count () );

        // Debug: Check all available kategori_sparepart.kode values
        console ( "All kategori codes:" );
        $allKategoriCodes   = [];
        $allKategoriFromATB = $ATB_Current->map ( function ($item) use (&$allKategoriCodes)
        {
            if ( $item->masterDataSparepart && $item->masterDataSparepart->kategoriSparepart )
            {
                $code = $item->masterDataSparepart->kategoriSparepart->kode;
                if ( ! in_array ( $code, $allKategoriCodes ) )
                {
                    $allKategoriCodes[] = $code;
                }
                return $code;
            }
            return null;
        } )->filter ()->unique ()->values ()->toArray ();
        console ( json_encode ( $allKategoriCodes ) );

        // Check specifically for Tyre-related items
        $tyreItems = $ATB_Current->filter ( function ($item)
        {
            return $item->masterDataSparepart &&
                $item->masterDataSparepart->kategoriSparepart &&
                ( strtoupper ( $item->masterDataSparepart->kategoriSparepart->kode ) == 'B3' ||
                    stripos ( $item->masterDataSparepart->kategoriSparepart->nama, 'TYRE' ) !== false );
        } );
        console ( "Tyre items in ATB_Current: " . $tyreItems->count () );
        if ( $tyreItems->count () > 0 )
        {
            $firstTyre = $tyreItems->first ();
            console ( "First Tyre item details:" );
            console ( "Kode: " . ( $firstTyre->masterDataSparepart->kategoriSparepart->kode ?? 'NULL' ) );
            console ( "Nama: " . ( $firstTyre->masterDataSparepart->kategoriSparepart->nama ?? 'NULL' ) );
            console ( "Quantity: " . ( $firstTyre->quantity ?? 'NULL' ) );
            console ( "Harga: " . ( $firstTyre->harga ?? 'NULL' ) );
        }

        $this->sums_current = [];
        $this->sums_before  = [];

        foreach ( $this->data as $category )
        {
            // Debug: Print current category
            console ( "Processing category: " . $category[ 'kode' ] . " - " . $category[ 'nama' ] );

            // For debugging Tyre specifically
            if ( strtoupper ( $category[ 'kode' ] ) === 'B3' )
            {
                console ( "===== DETAILED TYRE DEBUG =====" );
            }

            // Case insensitive comparison with safety checks
            $categoryItemsATB = $ATB_Current->filter ( function ($item) use ($category)
            {
                $hasRelations = $item->masterDataSparepart &&
                    $item->masterDataSparepart->kategoriSparepart;

                if ( ! $hasRelations )
                {
                    return false;
                }

                $dbCode       = trim ( $item->masterDataSparepart->kategoriSparepart->kode );
                $categoryCode = trim ( $category[ 'kode' ] );
                $matches = strtoupper ( $dbCode ) === strtoupper ( $categoryCode );

                // Debug Tyre matching specifically
                if ( strtoupper ( $categoryCode ) === 'B3' )
                {
                    console ( "Checking ATB item - DB code: " . $dbCode . ", Category code: " . $categoryCode . ", Matches: " . ( $matches ? 'YES' : 'NO' ) );
                }

                return $matches;
            } );

            // For Tyre, dump all item codes being compared
            if ( strtoupper ( $category[ 'kode' ] ) === 'B3' )
            {
                console ( "ATB items matching Tyre: " . $categoryItemsATB->count () );
                $tyreCodesFromATB = $ATB_Current->map ( function ($item)
                {
                    if ( $item->masterDataSparepart && $item->masterDataSparepart->kategoriSparepart )
                    {
                        return $item->masterDataSparepart->kategoriSparepart->kode;
                    }
                    return null;
                } )->filter ()->unique ()->values ()->toArray ();
                console ( "All codes in ATB: " . json_encode ( $tyreCodesFromATB ) );
            }

            // Calculate ATB Value
            $atbValue = $categoryItemsATB->sum ( function ($item)
            {
                $value = $item->quantity * $item->harga;
                if ( strtoupper ( $item->masterDataSparepart->kategoriSparepart->kode ) === 'B3' )
                {
                    console ( "Adding to TYRE ATB value: " . $value . " (qty:" . $item->quantity . " x price:" . $item->harga . ")" );
                }
                return $value;
            } );

            if ( strtoupper ( $category[ 'kode' ] ) === 'B3' )
            {
                console ( "Final TYRE ATB Value: " . $atbValue );
                console ( "===== END TYRE DEBUG =====" );
            }

            // Calculate APB Value (only accepted items)
            $categoryItemsAPB = $APB_Current->filter ( function ($item) use ($category)
            {
                return $item->masterDataSparepart &&
                    $item->masterDataSparepart->kategoriSparepart &&
                    strtoupper ( trim ( $item->masterDataSparepart->kategoriSparepart->kode ) ) === strtoupper ( trim ( $category[ 'kode' ] ) );
            } );

            // For debugging Tyre specifically
            if ( strtoupper ( $category[ 'kode' ] ) === 'B3' )
            {
                \Log::info ( 'TYRE ATB Count: ' . $categoryItemsATB->count () );
                if ( $categoryItemsATB->count () > 0 )
                {
                    $first = $categoryItemsATB->first ();
                    \Log::info ( 'TYRE ATB First Item Kode: ' . ( $first->masterDataSparepart->kategoriSparepart->kode ?? 'NULL' ) );
                }
            }

            // Calculate ATB Value
            $atbValue = $categoryItemsATB->sum ( function ($item)
            {
                return $item->quantity * $item->harga;
            } );

            // Calculate APB Value (only accepted items)
            $apbValue = $categoryItemsAPB->whereNotIn ( 'status', [ 'pending', 'rejected' ] )->sum ( function ($item)
            {
                return $item->quantity * $item->saldo->harga;
            } );

            // Calculate Saldo as ATB - APB
            $saldoValue = $atbValue - $apbValue;

            $this->sums_current[ $category[ 'kode' ] ] = [ 
                'nama'     => $category[ 'nama' ],
                'jenis'    => $category[ 'jenis' ],
                'subJenis' => $category[ 'subJenis' ],
                'atb'      => $atbValue,
                'apb'      => $apbValue,
                'saldo'    => $saldoValue
            ];

            // Calculate for previous period with the same robust comparison
            $categoryItemsATB_Before = $ATB_Before->filter ( function ($item) use ($category)
            {
                return $item->masterDataSparepart &&
                    $item->masterDataSparepart->kategoriSparepart &&
                    strtoupper ( trim ( $item->masterDataSparepart->kategoriSparepart->kode ) ) === strtoupper ( trim ( $category[ 'kode' ] ) );
            } );

            $categoryItemsAPB_Before = $APB_Before->filter ( function ($item) use ($category)
            {
                return $item->masterDataSparepart &&
                    $item->masterDataSparepart->kategoriSparepart &&
                    strtoupper ( trim ( $item->masterDataSparepart->kategoriSparepart->kode ) ) === strtoupper ( trim ( $category[ 'kode' ] ) );
            } );

            // Calculate ATB Value for before period
            $atbValueBefore = $categoryItemsATB_Before->sum ( function ($item)
            {
                return $item->quantity * $item->harga;
            } );

            // Calculate APB Value for before period (only accepted items)
            $apbValueBefore = $categoryItemsAPB_Before->whereNotIn ( 'status', [ 'pending', 'rejected' ] )->sum ( function ($item)
            {
                return $item->quantity * $item->saldo->harga;
            } );

            // Calculate Saldo as ATB - APB for before period
            $saldoValueBefore = $atbValueBefore - $apbValueBefore;

            $this->sums_before[ $category[ 'kode' ] ] = [ 
                'nama'     => $category[ 'nama' ],
                'jenis'    => $category[ 'jenis' ],
                'subJenis' => $category[ 'subJenis' ],
                'atb'      => $atbValueBefore,
                'apb'      => $apbValueBefore,
                'saldo'    => $saldoValueBefore
            ];
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

        // Function to format values - always return the value, even for zeros
        $formatValue = function ($value)
        {
            return $value; // Return the value as is, including zeros
        };

        // Add text styles for section headers
        if ( $row[ 'kode' ] === 'section_header' )
        {
            return [ 
                $row[ 'number' ],
                $row[ 'nama' ], // No HTML tags needed
                $formatValue ( $row[ 'before_atb' ] ),
                $formatValue ( $row[ 'before_apb' ] ),
                $formatValue ( $row[ 'before_saldo' ] ),
                $formatValue ( $row[ 'current_atb' ] ),
                $formatValue ( $row[ 'current_apb' ] ),
                $formatValue ( $row[ 'current_saldo' ] ),
                $formatValue ( $row[ 'before_atb' ] + $row[ 'current_atb' ] ),
                $formatValue ( $row[ 'before_apb' ] + $row[ 'current_apb' ] ),
                $formatValue ( $row[ 'before_saldo' ] + $row[ 'current_saldo' ] )
            ];
        }
        // Special handling for grand total row
        elseif ( $row[ 'kode' ] === 'total_row' )
        {
            return [ 
                '',
                $row[ 'nama' ], // TOTAL
                $formatValue ( $row[ 'before_atb' ] ),
                $formatValue ( $row[ 'before_apb' ] ),
                $formatValue ( $row[ 'before_saldo' ] ),
                $formatValue ( $row[ 'current_atb' ] ),
                $formatValue ( $row[ 'current_apb' ] ),
                $formatValue ( $row[ 'current_saldo' ] ),
                $formatValue ( $row[ 'before_atb' ] + $row[ 'current_atb' ] ),
                $formatValue ( $row[ 'before_apb' ] + $row[ 'current_apb' ] ),
                $formatValue ( $row[ 'before_saldo' ] + $row[ 'current_saldo' ] )
            ];
        }

        // Regular items remain unchanged
        return [ 
            $row[ 'number' ],
            $row[ 'nama' ],
            $formatValue ( $before[ 'atb' ] ),
            $formatValue ( $before[ 'apb' ] ),
            $formatValue ( $before[ 'saldo' ] ),
            $formatValue ( $current[ 'atb' ] ),
            $formatValue ( $current[ 'apb' ] ),
            $formatValue ( $current[ 'saldo' ] ),
            $formatValue ( $before[ 'atb' ] + $current[ 'atb' ] ),
            $formatValue ( $before[ 'apb' ] + $current[ 'apb' ] ),
            $formatValue ( $before[ 'saldo' ] + $current[ 'saldo' ] )
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
                        ],
                        // CHANGE: Add Tyre as a direct item within PEMELIHARAAN rather than a separate subsection
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

        // Calculate grand totals
        $grand_total_before  = [ 'atb' => 0, 'apb' => 0, 'saldo' => 0 ];
        $grand_total_current = [ 'atb' => 0, 'apb' => 0, 'saldo' => 0 ];

        foreach ( $this->sums_current as $key => $category )
        {
            // Sum up all previous month values
            $grand_total_before[ 'atb' ] += $this->sums_before[ $key ][ 'atb' ];
            $grand_total_before[ 'apb' ] += $this->sums_before[ $key ][ 'apb' ];
            $grand_total_before[ 'saldo' ] += $this->sums_before[ $key ][ 'saldo' ];

            // Sum up all current month values
            $grand_total_current[ 'atb' ] += $category[ 'atb' ];
            $grand_total_current[ 'apb' ] += $category[ 'apb' ];
            $grand_total_current[ 'saldo' ] += $category[ 'saldo' ];
        }

        // Add grand total row
        $rows[] = [ 
            'kode'          => 'total_row',
            'nama'          => 'TOTAL',
            'level'         => 0,
            'number'        => '',
            'before_atb'    => $grand_total_before[ 'atb' ],
            'before_apb'    => $grand_total_before[ 'apb' ],
            'before_saldo'  => $grand_total_before[ 'saldo' ],
            'current_atb'   => $grand_total_current[ 'atb' ],
            'current_apb'   => $grand_total_current[ 'apb' ],
            'current_saldo' => $grand_total_current[ 'saldo' ],
        ];

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

        // Format currency columns to show zeros as 0 and negatives in parentheses:
        $currencyFormat = '#,##0.00_-;(#,##0.00);0.00';
        foreach ( range ( 'D', 'L' ) as $col )
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
                ( $row[ 'kode' ] === 'section_header' &&
                    in_array ( $row[ 'nama' ], [ 
                        'SUKU CADANG',
                        'PERBAIKAN',
                        'PEMELIHARAAN',
                        'Maintenance Kit',
                        'Oil & Lubricants',
                        'TYRE',
                        'MATERIAL'
                    ] ) ) ||
                // Add condition to make TYRE row bold
                $row[ 'kode' ] === 'B3'
            )
            {
                $sheet->getStyle ( "B{$currentRow}:L{$currentRow}" )
                    ->getFont ()
                    ->setBold ( true );
            }
            $currentRow++;
        }

        // Set up grouping levels only (without hiding rows)
        $currentRow  = 8;
        $collection  = $this->collection ();
        $groupStarts = [];

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
                    'Maintenance Kit', 'Oil & Lubricants', 'TYRE' => 3, // TYRE at same level as other subsections
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

        // Style for total row
        $sheet->mergeCells ( "B{$lastRow}:C{$lastRow}" ); // Merge first two columns for the total row
        $sheet->setCellValue ( "B{$lastRow}", "TOTAL" ); // Explicitly set the TOTAL text

        $sheet->getStyle ( "B{$lastRow}:L{$lastRow}" )->applyFromArray ( [ 
            'font'      => [ 
                'bold' => true,
            ],
            'alignment' => [ 
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
            'borders'   => [ 
                'allBorders' => [ 
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
                'top'        => [ 
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                ],
            ],
        ] );

        // Center the "TOTAL" text
        $sheet->getStyle ( "B{$lastRow}" )->getAlignment ()
            ->setHorizontal ( \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER );

        // Right-align the numeric values
        $sheet->getStyle ( "D{$lastRow}:L{$lastRow}" )->getAlignment ()
            ->setHorizontal ( \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT );

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
