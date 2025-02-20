<?php

namespace App\Exports;

use App\Models\RKB;
use App\Models\DetailRKBUrgent;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;

class DetailRKBUrgentExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithCustomStartCell
{
    protected $rkbId;

    public function __construct ( $rkbId )
    {
        $this->rkbId = $rkbId;
    }

    public function startCell () : string
    {
        return 'B2';
    }

    public function collection ()
    {
        return DetailRKBUrgent::query ()
            ->select ( [ 
                'detail_rkb_urgent.*',
                'master_data_alat.jenis_alat',
                'master_data_alat.kode_alat',
                'kategori_sparepart.kode as kategori_kode',
                'kategori_sparepart.nama as kategori_nama',
                'master_data_sparepart.nama as sparepart_nama',
                'master_data_sparepart.part_number',
                'master_data_sparepart.merk'
            ] )
            ->join ( 'link_rkb_detail', 'detail_rkb_urgent.id', '=', 'link_rkb_detail.id_detail_rkb_urgent' )
            ->join ( 'link_alat_detail_rkb', 'link_rkb_detail.id_link_alat_detail_rkb', '=', 'link_alat_detail_rkb.id' )
            ->join ( 'master_data_alat', 'link_alat_detail_rkb.id_master_data_alat', '=', 'master_data_alat.id' )
            ->join ( 'kategori_sparepart', 'detail_rkb_urgent.id_kategori_sparepart_sparepart', '=', 'kategori_sparepart.id' )
            ->join ( 'master_data_sparepart', 'detail_rkb_urgent.id_master_data_sparepart', '=', 'master_data_sparepart.id' )
            ->where ( 'link_alat_detail_rkb.id_rkb', $this->rkbId )
            ->orderBy ( 'detail_rkb_urgent.updated_at', 'desc' )
            ->orderBy ( 'detail_rkb_urgent.id', 'desc' )
            ->get ();
    }

    public function headings () : array
    {
        $rkb     = RKB::with ( 'proyek' )->find ( $this->rkbId );
        $periode = \Carbon\Carbon::parse ( $rkb->periode )->locale ( 'id' )->translatedFormat ( 'F Y' );

        return [ 
            [ 'DETAIL RKB URGENT' ],
            [ '' ],
            [ 'Nomor RKB', ':', $rkb->nomor ?? '-' ],
            [ 'Nama Proyek', ':', $rkb->proyek->nama ?? '-' ],
            [ 'Periode', ':', $periode ],
            [ '' ],
            [ 
                'Nama Alat',
                'Kode Alat',
                'Kategori Sparepart',
                'Sparepart',
                'Part Number',
                'Merk',
                'Nama Koordinator',
                'Kronologi',
                'Quantity Requested',
                'Quantity Approved',
                'Satuan'
            ],
        ];
    }

    public function map ( $row ) : array
    {
        return [ 
            $row->jenis_alat ?? '-',
            $row->kode_alat ?? '-',
            ( $row->kategori_kode ? $row->kategori_kode . ': ' : '' ) . ( $row->kategori_nama ?? '-' ),
            $row->sparepart_nama ?? '-',
            $row->part_number ?? '-',
            $row->merk ?? '-',
            $row->nama_koordinator ?? '-',
            $row->kronologi ?? '-',
            $row->quantity_requested ?? '-',
            $row->quantity_approved ?? '-',
            $row->satuan ?? '-'
        ];
    }

    public function styles ( Worksheet $sheet )
    {
        $lastRow    = $sheet->getHighestRow ();
        $lastColumn = $sheet->getHighestColumn ();

        // Style for title
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

        // Style for RKB details
        $sheet->getStyle ( 'B4:B6' )->getFont ()->setBold ( true );
        $sheet->getStyle ( 'C4:C6' )->getAlignment ()->setHorizontal ( \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER );

        // Style for headers (now at row 8)
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

        // Style for data cells (starting from row 9)
        $sheet->getStyle ( 'B9:L' . $lastRow )->applyFromArray ( [ 
            'alignment' => [ 
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'wrapText'   => true,  // Enable text wrapping for all cells
            ],
            'borders'   => [ 
                'allBorders' => [ 
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ] );

        // Only set fixed width for Kronologi column
        $sheet->getColumnDimension ( 'I' )->setWidth ( 50 );
        $sheet->getStyle ( 'I8:I' . $lastRow )->getAlignment ()->setWrapText ( true );

        // Auto-adjust row heights for all rows with content
        for ( $row = 8; $row <= $lastRow; $row++ )
        {
            $sheet->getRowDimension ( $row )->setRowHeight ( -1 );
        }

        // Auto-adjust column widths (except for column I which has fixed width)
        foreach ( range ( 'B', $lastColumn ) as $column )
        {
            if ( $column != 'I' )
            {
                $sheet->getColumnDimension ( $column )->setAutoSize ( true );
            }
        }

        return [];
    }
}
