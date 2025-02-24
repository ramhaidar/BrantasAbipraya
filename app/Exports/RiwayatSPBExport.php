<?php

namespace App\Exports;

use App\Models\SPB;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;

class RiwayatSPBExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithCustomStartCell, WithEvents
{
    protected $spbId;
    protected $totalHarga = 0;
    protected $totalJumlahHarga = 0;
    protected $ppn = 0;
    protected $grandTotal = 0;

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

        // Calculate totals
        $collection = collect ( $spb->linkSpbDetailSpb );
        $this->calculateTotals ( $collection );

        return $collection;
    }

    private function calculateTotals ( $collection )
    {
        foreach ( $collection as $item )
        {
            $this->totalHarga += $item->detailSpb->harga;
            $this->totalJumlahHarga += $item->detailSpb->quantity_po * $item->detailSpb->harga;
        }
        $this->ppn        = $this->totalJumlahHarga * 0.11;
        $this->grandTotal = $this->totalJumlahHarga + $this->ppn;
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

        // Add totals at the bottom
        $totalRow = $lastRow + 1;
        $sheet->setCellValue ( "B{$totalRow}", 'Jumlah' );
        $sheet->setCellValue ( "H{$totalRow}", $this->totalHarga );
        $sheet->setCellValue ( "I{$totalRow}", $this->totalJumlahHarga );

        $ppnRow = $totalRow + 1;
        $sheet->setCellValue ( "B{$ppnRow}", 'PPN 11%' );
        $sheet->setCellValue ( "I{$ppnRow}", $this->ppn );

        $grandTotalRow = $ppnRow + 1;
        $sheet->setCellValue ( "B{$grandTotalRow}", 'Grand Total' );
        $sheet->setCellValue ( "I{$grandTotalRow}", $this->grandTotal );

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

        // Format currency columns
        $currencyFormat = '#,##0';
        $sheet->getStyle ( "G10:I{$grandTotalRow}" )
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
