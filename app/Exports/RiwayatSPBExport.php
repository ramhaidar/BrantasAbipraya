<?php

namespace App\Exports;

use App\Models\SPB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RiwayatSPBExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithCustomStartCell, WithEvents, WithStrictNullComparison
{
    protected $spbId;

    public function __construct ( $spbId )
    {
        $this->spbId = $spbId;
    }

    public function collection ()
    {
        $spb = SPB::with ( [ 
            'linkSpbDetailSpb.detailSpb.masterDataSparepart',
            'linkRkbSpbs.rkb.proyek',
            'masterDataSupplier'
        ] )->findOrFail ( $this->spbId );

        return collect ( $spb->linkSpbDetailSpb );
    }

    public function startCell () : string
    {
        return 'B2';
    }

    public function headings () : array
    {
        $spb = SPB::with ( [ 'linkRkbSpbs.rkb', 'masterDataSupplier' ] )->find ( $this->spbId );

        return [ 
            [ 'SURAT PEMESANAN BARANG' ],
            [ '' ],
            [ 'Lampiran:', 'Surat Pemesanan Barang' ],
            [ 'Nomor:', $spb->nomor ?? '-' ],
            [ 'Tanggal:', \Carbon\Carbon::parse ( $spb->tanggal )->isoFormat ( 'DD MMMM YYYY' ) ],
            [ 'Supplier:', $spb->masterDataSupplier->nama ?? '-' ],
            [ '' ],
            [ 
                'NO',
                'JENIS BARANG',
                'MERK',
                'SPESIFIKASI/TIPE/NO SERI',
                'JUMLAH',
                'SAT',
                'HARGA',
                'JUMLAH HARGA'
            ],
        ];
    }

    public function map ( $row ) : array
    {
        static $index = 0;
        $index++;

        return [ 
            $index,
            $row->detailSpb->masterDataSparepart->nama ?? '-',
            $row->detailSpb->masterDataSparepart->merk ?? '-',
            $row->detailSpb->masterDataSparepart->part_number ?? '-',
            $row->detailSpb->quantity_po ?? '-',
            $row->detailSpb->satuan ?? '-',
            $row->detailSpb->harga ?? 0,
            ( $row->detailSpb->quantity_po * $row->detailSpb->harga ) ?? 0
        ];
    }

    public function styles ( Worksheet $sheet )
    {
        $lastRow    = $sheet->getHighestRow ();
        $lastColumn = 'I';

        // Style for title
        $sheet->mergeCells ( 'B2:I2' );
        $sheet->getStyle ( 'B2' )->applyFromArray ( [ 
            'font'      => [ 
                'bold' => true,
                'size' => 14,
            ],
            'alignment' => [ 
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ] );

        // Style for SPB details
        $sheet->getStyle ( 'B4:B6' )->getFont ()->setBold ( true );

        // Style for headers
        $sheet->getStyle ( "B9:{$lastColumn}9" )->applyFromArray ( [ 
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
        $sheet->getStyle ( "B10:{$lastColumn}{$lastRow}" )->applyFromArray ( [ 
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

        // Set right alignment for price columns
        $sheet->getStyle ( "H10:I{$lastRow}" )->getAlignment ()->setHorizontal ( \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT );

        // Add totals at the bottom using Excel formulas
        $totalRow = $lastRow + 1;
        $sheet->setCellValue ( "B{$totalRow}", 'Jumlah' );
        // Sum of HARGA column
        $sheet->setCellValue ( "H{$totalRow}", "=SUM(H10:H{$lastRow})" );
        // Sum of JUMLAH HARGA column
        $sheet->setCellValue ( "I{$totalRow}", "=SUM(I10:I{$lastRow})" );
        // Merge cells for "Jumlah" row
        $sheet->mergeCells ( "B{$totalRow}:G{$totalRow}" );
        // Center align the merged cells
        $sheet->getStyle ( "B{$totalRow}:G{$totalRow}" )->applyFromArray ( [ 
            'alignment' => [ 
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ] );

        $ppnRow = $totalRow + 1;
        $sheet->setCellValue ( "B{$ppnRow}", 'PPN 11%' );
        // Calculate PPN as 11% of total JUMLAH HARGA
        $sheet->setCellValue ( "I{$ppnRow}", "=I{$totalRow}*0.11" );
        // Merge cells for "PPN 11%" row
        $sheet->mergeCells ( "B{$ppnRow}:G{$ppnRow}" );
        // Center align the merged cells
        $sheet->getStyle ( "B{$ppnRow}:G{$ppnRow}" )->applyFromArray ( [ 
            'alignment' => [ 
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ] );

        $grandTotalRow = $ppnRow + 1;
        $sheet->setCellValue ( "B{$grandTotalRow}", 'Grand Total' );
        // Sum of JUMLAH HARGA and PPN
        $sheet->setCellValue ( "I{$grandTotalRow}", "=I{$totalRow}+I{$ppnRow}" );
        // Merge cells for "Grand Total" row
        $sheet->mergeCells ( "B{$grandTotalRow}:G{$grandTotalRow}" );
        // Center align the merged cells
        $sheet->getStyle ( "B{$grandTotalRow}:G{$grandTotalRow}" )->applyFromArray ( [ 
            'alignment' => [ 
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ] );

        // Style for totals
        $sheet->getStyle ( "B{$totalRow}:I{$grandTotalRow}" )->applyFromArray ( [ 
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

        // Set right alignment for total price columns
        $sheet->getStyle ( "H{$totalRow}:I{$grandTotalRow}" )->getAlignment ()->setHorizontal ( \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT );

        // Format currency columns
        $currencyFormat = '#,##0';
        $sheet->getStyle ( "H10:I{$grandTotalRow}" )
            ->getNumberFormat ()
            ->setFormatCode ( $currencyFormat );

        // Auto-size columns
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
