<?php

namespace App\Exports;

use App\Models\ATB;
use App\Models\Proyek;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ATBHutangUnitAlatExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithCustomStartCell, WithEvents, WithStrictNullComparison
{
    protected $proyekId;
    protected $rowspanGroups;
    protected $totalHarga = 0;
    protected $totalPpn = 0;
    protected $totalBruto = 0;

    public function __construct ( $proyekId )
    {
        $this->proyekId      = $proyekId;
        $this->rowspanGroups = collect ();
    }

    public function collection ()
    {
        $data = ATB::with ( [ 
            'spb',
            'masterDataSparepart.kategoriSparepart',
            'masterDataSupplier',
            'detailSpb',
        ] )
            ->where ( 'id_proyek', $this->proyekId )
            ->where ( 'tipe', 'hutang-unit-alat' )
            ->orderBy ( 'tanggal', 'desc' )
            ->orderBy ( 'updated_at', 'desc' )
            ->orderBy ( 'id', 'desc' )
            ->get ();

        // Group items by SPB number
        $groupedBySpb = $data->groupBy ( function ($item)
        {
            return $item->spb->nomor ?? 'No SPB';
        } );

        // Store rowspan information
        $this->rowspanGroups = $groupedBySpb->map ( function ($group)
        {
            return $group->count ();
        } );

        return $data;
    }

    public function startCell () : string
    {
        return 'B2';
    }

    public function headings () : array
    {
        $proyek = Proyek::find ( $this->proyekId );

        return [ 
            [ 'DATA ATB HUTANG UNIT ALAT' ],
            [ '' ],
            [ 'Nama Proyek', ':', $proyek->nama ?? '-' ],
            [ '' ],
            [ 
                'Nomor SPB',
                'Tanggal',
                'Kode',
                'Supplier',
                'Sparepart',
                'Merk',
                'Part Number',
                'Quantity',
                'Satuan',
                'Harga',
                'Jumlah Harga',
                'PPN',
                'Bruto',
            ],
        ];
    }

    public function map ( $row ) : array
    {
        $spbNomor = $row->spb->nomor ?? '-';

        // Calculate values without formatting
        $jumlahHarga = $row->quantity * $row->harga;
        $ppn         = $jumlahHarga * 0.11;
        $bruto       = $jumlahHarga * 1.11;

        // Update running totals
        $this->totalHarga += $jumlahHarga;
        $this->totalPpn += $ppn;
        $this->totalBruto += $bruto;

        return [ 
            $spbNomor,
            $row->tanggal ? \Carbon\Carbon::parse ( $row->tanggal )->translatedFormat ( 'l, d F Y' ) : '-',
            $row->masterDataSparepart->kategoriSparepart ?
            $row->masterDataSparepart->kategoriSparepart->kode . ': ' .
            $row->masterDataSparepart->kategoriSparepart->nama : '-',
            $row->masterDataSupplier->nama ?? '-',
            $row->masterDataSparepart->nama ?? '-',
            $row->masterDataSparepart->merk ?? '-',
            $row->masterDataSparepart->part_number ?? '-',
            $row->quantity ?? 0,
            $row->detailSpb->satuan ?? ( $row->saldo->satuan ?? '-' ),
            $row->harga, // Raw value, will be formatted by Excel
            $jumlahHarga, // Raw value
            $ppn, // Raw value
            $bruto // Raw value
        ];
    }

    public function styles ( Worksheet $sheet )
    {
        $lastRow    = $sheet->getHighestRow ();
        $lastColumn = $sheet->getHighestColumn ();

        // Style for title
        $sheet->mergeCells ( 'B2:N2' );
        $sheet->getStyle ( 'B2' )->applyFromArray ( [ 
            'font'      => [ 
                'bold' => true,
                'size' => 14,
            ],
            'alignment' => [ 
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ] );

        // Style for project name
        $sheet->getStyle ( 'B4' )->getFont ()->setBold ( true );
        $sheet->getStyle ( 'C4' )->getAlignment ()->setHorizontal ( \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER );

        // Style for headers
        $sheet->getStyle ( 'B6:N6' )->applyFromArray ( [ 
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
        ] );

        // Style for data cells
        $sheet->getStyle ( 'B7:N' . $lastRow )->applyFromArray ( [ 
            'alignment' => [ 
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'wrapText'   => true,
            ],
            'borders'   => [ 
                'allBorders' => [ 
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ] );

        // Right align numeric columns
        $numericColumns = [ 'K', 'L', 'M', 'N' ];
        foreach ( $numericColumns as $col )
        {
            $sheet->getStyle ( $col . '7:' . $col . $lastRow )
                ->getAlignment ()
                ->setHorizontal ( \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT );
        }

        // Format currency columns with proper Indonesian formatting
        $currencyFormat  = '#,##0';
        $currencyColumns = [ 'K', 'L', 'M', 'N' ];
        foreach ( $currencyColumns as $col )
        {
            $sheet->getStyle ( $col . '7:' . $col . $lastRow )
                ->getNumberFormat ()
                ->setFormatCode ( $currencyFormat );
        }

        // Apply rowspan merges for SPB number
        $currentRow = 7;
        foreach ( $this->rowspanGroups as $spbNumber => $rowspan )
        {
            if ( $rowspan > 1 )
            {
                $sheet->mergeCells ( "B{$currentRow}:B" . ( $currentRow + $rowspan - 1 ) );
                $sheet->getStyle ( "B{$currentRow}:B" . ( $currentRow + $rowspan - 1 ) )
                    ->getAlignment ()->setVertical ( 'center' );
            }
            $currentRow += $rowspan;
        }

        // Calculate and add totals row
        $totalRow = $lastRow + 1;
        $sheet->setCellValue ( "B{$totalRow}", "Grand Total" );
        $sheet->mergeCells ( "B{$totalRow}:K{$totalRow}" );

        // Center the Grand Total text both vertically and horizontally
        $sheet->getStyle ( "B{$totalRow}:K{$totalRow}" )->applyFromArray ( [ 
            'alignment' => [ 
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ]
        ] );

        // Set totals
        $sheet->setCellValue ( "J{$totalRow}", "=SUM(J7:J" . ( $totalRow - 1 ) . ")" );
        $sheet->setCellValue ( "L{$totalRow}", "=SUM(L7:L" . ( $totalRow - 1 ) . ")" );
        $sheet->setCellValue ( "M{$totalRow}", "=SUM(M7:M" . ( $totalRow - 1 ) . ")" );
        $sheet->setCellValue ( "N{$totalRow}", "=SUM(N7:N" . ( $totalRow - 1 ) . ")" );

        // Style for totals row
        $sheet->getStyle ( "B{$totalRow}:N{$totalRow}" )->applyFromArray ( [ 
            'font'    => [ 'bold' => true ],
            'fill'    => [ 
                'fillType'   => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [ 'rgb' => 'c0c0c0' ],
            ],
            'borders' => [ 
                'allBorders' => [ 
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ] );

        // Format the totals with currency format
        $sheet->getStyle ( "J{$totalRow}:N{$totalRow}" )
            ->getNumberFormat ()
            ->setFormatCode ( $currencyFormat );

        // Auto-adjust row heights
        for ( $row = 6; $row <= $lastRow; $row++ )
        {
            $sheet->getRowDimension ( $row )->setRowHeight ( -1 );
        }

        // Auto-adjust column widths
        foreach ( range ( 'B', $lastColumn ) as $column )
        {
            $sheet->getColumnDimension ( $column )->setAutoSize ( true );
        }

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
