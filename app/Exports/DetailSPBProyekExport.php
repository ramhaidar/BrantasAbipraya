<?php

namespace App\Exports;

use App\Models\RKB;
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
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class DetailSPBProyekExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithCustomStartCell, WithEvents, WithStrictNullComparison
{
    protected $rkbId;
    protected $totals;

    public function __construct ( $rkbId )
    {
        $this->rkbId  = $rkbId;
        $this->totals = [ 
            'totalHarga'       => 0,
            'totalJumlahHarga' => 0
        ];
    }

    public function collection ()
    {
        $spbs = SPB::with ( [ 
            'linkSpbDetailSpb.detailSpb.masterDataAlat',
            'linkSpbDetailSpb.detailSpb.masterDataSparepart.kategoriSparepart',
            'linkSpbDetailSpb.detailSpb.atbs',
            'masterDataSupplier',
            'originalSpb.addendums',
        ] )
            ->where ( 'is_addendum', false )
            ->whereIn ( 'id', RKB::find ( $this->rkbId )->spbs ()->pluck ( 'id_spb' ) )
            ->get ();

        // Prepare flattened data
        $flattenedData = collect ();
        foreach ( $spbs as $spb )
        {
            $details = isset ( $spb->originalSpb ) ? $spb->originalSpb->linkSpbDetailSpb : $spb->linkSpbDetailSpb;

            foreach ( $details as $detail )
            {
                $detailSpb   = $detail->detailSpb;
                $harga       = $detailSpb->harga;
                $jumlahHarga = $harga * $detailSpb->quantity_po;

                // Update totals
                $this->totals[ 'totalHarga' ] += $harga;
                $this->totals[ 'totalJumlahHarga' ] += $jumlahHarga;

                $flattenedData->push ( (object) [ 
                    'supplier'          => $spb->masterDataSupplier->nama,
                    'detail'            => $detailSpb,
                    'alat'              => $detailSpb->masterDataAlat,
                    'sparepart'         => $detailSpb->masterDataSparepart,
                    'kategori'          => $detailSpb->masterDataSparepart->kategoriSparepart,
                    'quantity_diterima' => $detailSpb->atbs->sum ( 'quantity' ),
                    'harga'             => $harga,
                    'jumlah_harga'      => $jumlahHarga
                ] );
            }
        }

        return $flattenedData;
    }

    public function startCell () : string
    {
        return 'B2';
    }

    public function headings () : array
    {
        $rkb     = RKB::with ( 'proyek' )->find ( $this->rkbId );
        $periode = \Carbon\Carbon::parse ( $rkb->periode )->locale ( 'id' )->translatedFormat ( 'F Y' );

        return [ 
            [ 'DETAIL SPB PROYEK' ],
            [ '' ],
            [ 'Nomor RKB', ':', $rkb->nomor ?? '-' ],
            [ 'Nama Proyek', ':', $rkb->proyek->nama ?? '-' ],
            [ 'Periode', ':', $periode ],
            [ '' ],
            [ 
                'Nama Alat',
                'Kode Alat',
                'Kategori',
                'Sparepart PO',
                'Merk',
                'Supplier',
                'Quantity PO',
                'Quantity Diterima',
                'Satuan',
                'Harga',
                'Jumlah Harga'
            ],
        ];
    }

    public function map ( $row ) : array
    {
        return [ 
            $row->alat->jenis_alat ?? '-',
            $row->alat->kode_alat ?? '-',
            ( $row->kategori->kode ? $row->kategori->kode . ': ' : '' ) . ( $row->kategori->nama ?? '-' ),
            $row->sparepart->nama ?? '-',
            $row->sparepart->merk ?? '-',
            $row->supplier ?? '-',
            $row->detail->quantity_po ?? '-',
            $row->quantity_diterima ?? 0,
            $row->detail->satuan ?? '-',
            $row->harga ?? 0,  // Changed to numeric for Excel formatting
            $row->jumlah_harga ?? 0  // Changed to numeric for Excel formatting
        ];
    }

    public function styles ( Worksheet $sheet )
    {
        $lastRow    = $sheet->getHighestRow ();
        $lastColumn = $sheet->getHighestColumn ();

        // Title styling
        $sheet->mergeCells ( 'B2:L2' );
        $sheet->getStyle ( 'B2' )->applyFromArray ( [ 
            'font'      => [ 
                'bold' => true,
                'size' => 14,
            ],
            'alignment' => [ 
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ] );

        // RKB details styling
        $sheet->getStyle ( 'B4:B6' )->getFont ()->setBold ( true );
        $sheet->getStyle ( 'C4:C6' )->getAlignment ()->setHorizontal ( \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER );

        // Headers styling
        $sheet->getStyle ( 'B8:L8' )->applyFromArray ( [ 
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

        // Data cells styling
        $sheet->getStyle ( 'B9:L' . $lastRow )->applyFromArray ( [ 
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

        // Currency columns styling (Harga & Jumlah Harga) - Updated to match RiwayatSPBExport
        $currencyFormat = '#,##0';
        $sheet->getStyle ( 'K9:L' . $lastRow )->applyFromArray ( [ 
            'alignment' => [ 
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
                'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ]
        ] )->getNumberFormat ()->setFormatCode ( $currencyFormat );

        // Add totals with same currency format
        $totalRow = $lastRow + 1;
        $sheet->setCellValue ( 'B' . $totalRow, 'Jumlah' );
        $sheet->mergeCells ( 'B' . $totalRow . ':J' . $totalRow );
        $sheet->setCellValue ( 'K' . $totalRow, $this->totals[ 'totalHarga' ] );
        $sheet->setCellValue ( 'L' . $totalRow, $this->totals[ 'totalJumlahHarga' ] );

        // Add PPN with same currency format
        $ppnRow = $totalRow + 1;
        $ppn    = $this->totals[ 'totalJumlahHarga' ] * 0.11;
        $sheet->setCellValue ( 'B' . $ppnRow, 'PPN 11%' );
        $sheet->mergeCells ( 'B' . $ppnRow . ':J' . $ppnRow );
        $sheet->setCellValue ( 'L' . $ppnRow, $ppn );

        // Add grand total with same currency format
        $grandTotalRow = $ppnRow + 1;
        $sheet->setCellValue ( 'B' . $grandTotalRow, 'Grand Total' );
        $sheet->mergeCells ( 'B' . $grandTotalRow . ':J' . $grandTotalRow );
        $sheet->setCellValue ( 'L' . $grandTotalRow, $this->totals[ 'totalJumlahHarga' ] + $ppn );

        // Apply currency format to footer totals
        $sheet->getStyle ( "K{$totalRow}:L{$grandTotalRow}" )
            ->getNumberFormat ()
            ->setFormatCode ( $currencyFormat );

        // Style the footer rows
        $sheet->getStyle ( 'B' . $totalRow . ':L' . $grandTotalRow )->applyFromArray ( [ 
            'font'      => [ 'bold' => true ],
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

        // Right align the currency columns in footer
        $sheet->getStyle ( "K{$totalRow}:L{$grandTotalRow}" )->applyFromArray ( [ 
            'alignment' => [ 
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
                'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ]
        ] );

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
