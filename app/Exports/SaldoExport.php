<?php

namespace App\Exports;

use App\Models\Saldo;
use App\Models\Proyek;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SaldoExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithCustomStartCell, WithEvents, WithStrictNullComparison
{
    protected $proyekId;
    protected $tipe;
    protected $totalHarga = 0;

    public function __construct ( $proyekId, $tipe )
    {
        $this->proyekId = $proyekId;
        $this->tipe     = $tipe;
    }

    public function collection ()
    {
        return Saldo::with ( [ 
            'atb',
            'masterDataSparepart.kategoriSparepart',
            'masterDataSupplier'
        ] )
            ->where ( 'id_proyek', $this->proyekId )
            ->where ( 'tipe', $this->tipe )
            ->orderBy ( 'created_at', 'desc' )
            ->get ();
    }

    public function startCell () : string
    {
        return 'B2';
    }

    public function headings () : array
    {
        $proyek    = Proyek::find ( $this->proyekId );
        $typeTitle = ucwords ( str_replace ( '-', ' ', $this->tipe ) );

        return [ 
            [ "DATA SALDO $typeTitle" ],
            [ '' ],
            [ 'Nama Proyek', ':', $proyek->nama ?? '-' ],
            [ '' ],
            [ 
                'Tanggal',
                'Kode',
                'Supplier',
                'Sparepart',
                'Merk',
                'Part Number',
                'Quantity',
                'Satuan',
                'Harga',
                'Jumlah Harga'
            ],
        ];
    }

    public function map ( $row ) : array
    {
        // Calculate total
        $jumlahHarga      = $row->quantity * $row->harga;
        $this->totalHarga += $jumlahHarga;

        return [ 
            $row->atb->tanggal ? \Carbon\Carbon::parse ( $row->atb->tanggal )->translatedFormat ( 'l, d F Y' ) : '-',
            $row->masterDataSparepart->kategoriSparepart ?
            $row->masterDataSparepart->kategoriSparepart->kode . ': ' .
            $row->masterDataSparepart->kategoriSparepart->nama : '-',
            $row->masterDataSupplier->nama ?? '-',
            $row->masterDataSparepart->nama ?? '-',
            $row->masterDataSparepart->merk ?? '-',
            $row->masterDataSparepart->part_number ?? '-',
            $row->quantity ?? 0,
            $row->satuan ?? '-',
            $row->harga ?? 0,
            $jumlahHarga
        ];
    }

    public function styles ( Worksheet $sheet )
    {
        $lastRow    = $sheet->getHighestRow ();
        $lastColumn = 'K';

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

        // Style for project name
        $sheet->getStyle ( 'B4' )->getFont ()->setBold ( true );
        $sheet->getStyle ( 'C4' )->getAlignment ()->setHorizontal ( \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER );

        // Style for headers
        $sheet->getStyle ( "B6:{$lastColumn}6" )->applyFromArray ( [ 
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
        $sheet->getStyle ( "B7:{$lastColumn}{$lastRow}" )->applyFromArray ( [ 
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
        $numericColumns = [ 'J', 'K' ];
        foreach ( $numericColumns as $col )
        {
            $sheet->getStyle ( $col . '7:' . $col . $lastRow )
                ->getAlignment ()
                ->setHorizontal ( \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT );
        }

        // Format currency columns
        $currencyFormat  = '#,##0';
        $currencyColumns = [ 'J', 'K' ];
        foreach ( $currencyColumns as $col )
        {
            $sheet->getStyle ( $col . '7:' . $col . $lastRow )
                ->getNumberFormat ()
                ->setFormatCode ( $currencyFormat );
        }

        // Add totals row
        $totalRow = $lastRow + 1;
        $sheet->setCellValue ( "B{$totalRow}", "Grand Total" );
        $sheet->mergeCells ( "B{$totalRow}:J{$totalRow}" );
        $sheet->setCellValue ( "K{$totalRow}", "=SUM(K7:K" . ( $totalRow - 1 ) . ")" );

        // Style for totals row
        $sheet->getStyle ( "B{$totalRow}:{$lastColumn}{$totalRow}" )->applyFromArray ( [ 
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

        // Center align grand total text
        $sheet->getStyle ( "B{$totalRow}:J{$totalRow}" )->applyFromArray ( [ 
            'alignment' => [ 
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ]
        ] );

        // Format totals with currency format
        $sheet->getStyle ( "K{$totalRow}" )
            ->getNumberFormat ()
            ->setFormatCode ( $currencyFormat );

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
