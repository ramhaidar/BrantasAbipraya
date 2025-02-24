<?php

namespace App\Exports;

use App\Models\RKB;
use App\Models\DetailRKBGeneral;
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

class DetailRKBGeneralExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithCustomStartCell, WithEvents, WithStrictNullComparison
{
    protected $rkbId;

    public function __construct ( $rkbId )
    {
        $this->rkbId = $rkbId;
    }

    public function collection ()
    {
        return DetailRKBGeneral::query ()
            ->select ( [ 
                'detail_rkb_general.*',
                'master_data_alat.jenis_alat',
                'master_data_alat.kode_alat',
                'kategori_sparepart.kode as kategori_kode',
                'kategori_sparepart.nama as kategori_nama',
                'master_data_sparepart.nama as sparepart_nama',
                'master_data_sparepart.part_number',
                'master_data_sparepart.merk'
            ] )
            ->join ( 'link_rkb_detail', 'detail_rkb_general.id', '=', 'link_rkb_detail.id_detail_rkb_general' )
            ->join ( 'link_alat_detail_rkb', 'link_rkb_detail.id_link_alat_detail_rkb', '=', 'link_alat_detail_rkb.id' )
            ->join ( 'master_data_alat', 'link_alat_detail_rkb.id_master_data_alat', '=', 'master_data_alat.id' )
            ->join ( 'kategori_sparepart', 'detail_rkb_general.id_kategori_sparepart_sparepart', '=', 'kategori_sparepart.id' )
            ->join ( 'master_data_sparepart', 'detail_rkb_general.id_master_data_sparepart', '=', 'master_data_sparepart.id' )
            ->where ( 'link_alat_detail_rkb.id_rkb', $this->rkbId )
            ->orderBy ( 'detail_rkb_general.updated_at', 'desc' )
            ->orderBy ( 'detail_rkb_general.id', 'desc' )
            ->get ();
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
            [ 'DETAIL RKB GENERAL' ],
            [ '' ],
            [ 'Nomor RKB', ':', $rkb->nomor ?? '-' ],
            [ 'Nama Proyek', ':', $rkb->proyek->nama ?? '-' ],
            [ 'Periode', ':', $periode ],
            [ 'Status', ':', $this->getStatusText ( $rkb ) ],
            [ '' ],
            [ 
                'Nama Alat',
                'Kode Alat',
                'Kategori Sparepart',
                'Sparepart',
                'Part Number',
                'Merk',
                'Quantity Requested',
                'Quantity Approved',
                'Satuan'
            ],
        ];
    }

    private function getStatusText ( $rkb )
    {
        if ( ! $rkb->is_finalized && ! $rkb->is_evaluated && ! $rkb->is_approved_vp && ! $rkb->is_approved_svp )
        {
            return 'Pengajuan';
        }
        elseif ( $rkb->is_finalized && ! $rkb->is_approved_svp )
        {
            return 'Evaluasi';
        }
        elseif ( $rkb->is_finalized && $rkb->is_evaluated && $rkb->is_approved_vp && $rkb->is_approved_svp )
        {
            return 'Disetujui';
        }
        else
        {
            return 'Tidak Diketahui';
        }
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
        $sheet->mergeCells ( 'B2:J2' );
        $sheet->getStyle ( 'B2' )->applyFromArray ( [ 
            'font'      => [ 
                'bold' => true,
                'size' => 14,
            ],
            'alignment' => [ 
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ] );

        // Style for RKB details (updated row range to include Status)
        $sheet->getStyle ( 'B4:B7' )->getFont ()->setBold ( true );
        $sheet->getStyle ( 'C4:C7' )->getAlignment ()->setHorizontal ( \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER );

        // Style for status cell
        $sheet->getStyle ( 'D7' )->applyFromArray ( [ 
            'font' => [ 
                'bold'  => true,
                'color' => [ 'rgb' => '000000' ],
            ],
        ] );

        // Style for headers (now at row 9 instead of 8)
        $sheet->getStyle ( 'B9:J9' )->applyFromArray ( [ 
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

        // Style for data cells (starting from row 10 instead of 9)
        $sheet->getStyle ( 'B10:J' . $lastRow )->applyFromArray ( [ 
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

        // Auto-adjust row heights for all rows with content (updated starting row)
        for ( $row = 9; $row <= $lastRow; $row++ )
        {
            $sheet->getRowDimension ( $row )->setRowHeight ( -1 );
        }

        // Auto-adjust column widths for all columns
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
