<?php

namespace App\Exports;

use App\Models\DetailRKBGeneral;
use App\Models\RKB;
use App\Models\Saldo;
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

class EvaluasiDetailRKBGeneralExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithCustomStartCell, WithEvents, WithStrictNullComparison
{
    protected $rkbId;
    protected $stockQuantities;
    protected $rowspanGroups;

    public function __construct ( $rkbId )
    {
        $this->rkbId = $rkbId;
        $this->loadStockQuantities ();
        $this->rowspanGroups = collect ();
    }

    private function loadStockQuantities ()
    {
        $rkb                   = RKB::find ( $this->rkbId );
        $this->stockQuantities = Saldo::where ( 'id_proyek', $rkb->id_proyek )
            ->get ()
            ->groupBy ( 'id_master_data_sparepart' )
            ->map ( function ($items)
            {
                return $items->sum ( 'quantity' );
            } );
    }

    public function collection ()
    {
        $data = DetailRKBGeneral::query ()
            ->select ( [ 
                'detail_rkb_general.*',
                'master_data_alat.jenis_alat',
                'master_data_alat.kode_alat',
                'kategori_sparepart.kode as kategori_kode',
                'kategori_sparepart.nama as kategori_nama',
                'master_data_sparepart.nama as sparepart_nama',
                'master_data_sparepart.part_number',
                'master_data_sparepart.merk',
                'master_data_sparepart.id as sparepart_id',
                'detail_rkb_general.quantity_requested',
                'detail_rkb_general.quantity_approved'
            ] )
            ->join ( 'link_rkb_detail', 'detail_rkb_general.id', '=', 'link_rkb_detail.id_detail_rkb_general' )
            ->join ( 'link_alat_detail_rkb', 'link_rkb_detail.id_link_alat_detail_rkb', '=', 'link_alat_detail_rkb.id' )
            ->join ( 'master_data_alat', 'link_alat_detail_rkb.id_master_data_alat', '=', 'master_data_alat.id' )
            ->join ( 'kategori_sparepart', 'detail_rkb_general.id_kategori_sparepart_sparepart', '=', 'kategori_sparepart.id' )
            ->join ( 'master_data_sparepart', 'detail_rkb_general.id_master_data_sparepart', '=', 'master_data_sparepart.id' )
            ->where ( 'link_alat_detail_rkb.id_rkb', $this->rkbId )
            ->orderBy ( 'master_data_sparepart.part_number' )
            ->orderBy ( 'master_data_alat.jenis_alat' )
            ->orderBy ( 'master_data_alat.kode_alat' )
            ->get ();

        // Group items by part number first for stock quantities
        $groupedByPartNumber = $data->groupBy ( function ($item)
        {
            return $item->part_number;
        } );

        // Then group by part number AND equipment details
        $groupedItems = $data->groupBy ( function ($item)
        {
            return $item->part_number . '|' . $item->jenis_alat . '|' . $item->kode_alat;
        } );

        // Store rowspan information
        $this->rowspanGroups = $groupedByPartNumber->map ( function ($group) use ($groupedItems)
        {
            $partNumber = $group->first ()->part_number;
            return $groupedItems->filter ( function ($items, $key) use ($partNumber)
            {
                return explode ( '|', $key )[ 0 ] === $partNumber;
            } )->count ();
        } );

        return $data;
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
            [ 'EVALUASI RKB GENERAL' ],
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
                'Quantity in Stock',
                'Satuan'
            ],
        ];
    }

    private function getStatusText ( $rkb )
    {
        if ( $rkb->is_approved_svp ) return 'Approved by SVP';
        if ( $rkb->is_approved_vp ) return 'Approved by VP';
        if ( $rkb->is_evaluated ) return 'Sudah Evaluasi';
        return 'Evaluasi';
    }

    public function map ( $row ) : array
    {
        $partNumber = $row->part_number;
        $key        = $partNumber . '|' . $row->jenis_alat . '|' . $row->kode_alat;

        // Find position in the group
        $groupKeys        = $this->rowspanGroups->keys ()->toArray ();
        $groupIndex       = array_search ( $partNumber, $groupKeys );
        $currentGroupKeys = collect ( $this->collection () )
            ->filter ( function ($item) use ($partNumber)
            {
                return $item->part_number === $partNumber;
            } )
            ->groupBy ( function ($item)
            {
                return $item->part_number . '|' . $item->jenis_alat . '|' . $item->kode_alat;
            } )
            ->keys ()
            ->toArray ();
        $positionInGroup  = array_search ( $key, $currentGroupKeys );

        // Only include stock quantity for first row in group
        $stockQuantity = ( $positionInGroup === 0 )
            ? ( $this->stockQuantities[ $row->sparepart_id ] ?? '-' )
            : null;

        return [ 
            $row->jenis_alat ?? '-',
            $row->kode_alat ?? '-',
            ( $row->kategori_kode ? $row->kategori_kode . ': ' : '' ) . ( $row->kategori_nama ?? '-' ),
            $row->sparepart_nama ?? '-',
            $row->part_number ?? '-',
            $row->merk ?? '-',
            $row->quantity_requested ?? '-',
            $row->quantity_approved ?? '-',
            $stockQuantity,
            $row->satuan ?? '-'
        ];
    }

    public function styles ( Worksheet $sheet )
    {
        $lastRow    = $sheet->getHighestRow ();
        $lastColumn = $sheet->getHighestColumn ();

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

        // Style for RKB details
        $sheet->getStyle ( 'B4:B7' )->getFont ()->setBold ( true );
        $sheet->getStyle ( 'C4:C7' )->getAlignment ()->setHorizontal ( \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER );

        // Style for status cell
        $sheet->getStyle ( 'D7' )->applyFromArray ( [ 
            'font' => [ 
                'bold'  => true,
                'color' => [ 'rgb' => '000000' ],
            ],
        ] );

        // Style for headers
        $sheet->getStyle ( 'B9:K9' )->applyFromArray ( [ 
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
        $sheet->getStyle ( 'B10:K' . $lastRow )->applyFromArray ( [ 
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

        // Get RKB status for conditional formatting
        $rkb = RKB::find ( $this->rkbId );

        // Style for data cells with specific column colors
        $sheet->getStyle ( 'B10:K' . $lastRow )->applyFromArray ( [ 
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

        // Style for Quantity Requested column (Column G) - Light Yellow
        $sheet->getStyle ( 'H10:H' . $lastRow )->applyFromArray ( [ 
            'fill' => [ 
                'fillType'   => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [ 'rgb' => 'FFEB9C' ], // Light yellow
            ],
        ] );

        // Style for Quantity Approved column (Column H) - Conditional based on RKB status
        $approvedColumnStyle = [ 
            'fill' => [ 
                'fillType'   => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [ 
                    'rgb' =>
                        $rkb->is_approved_svp ? 'CFE2F3' :  // Light blue for SVP approved
                        ( $rkb->is_approved_vp ? 'D9EAD3' :  // Light green for VP approved
                            ( $rkb->is_evaluated ? 'D9D2E9' :    // Light purple for evaluated
                                'FFE599' ) )                          // Light orange for draft
                ],
            ],
        ];
        $sheet->getStyle ( 'I10:I' . $lastRow )->applyFromArray ( $approvedColumnStyle );

        // Auto-adjust row heights
        for ( $row = 9; $row <= $lastRow; $row++ )
        {
            $sheet->getRowDimension ( $row )->setRowHeight ( -1 );
        }

        // Auto-adjust column widths
        foreach ( range ( 'B', $lastColumn ) as $column )
        {
            $sheet->getColumnDimension ( $column )->setAutoSize ( true );
        }

        // Apply rowspan merges for stock quantity column
        $currentRow = 10; // Start after headers
        foreach ( $this->rowspanGroups as $partNumber => $rowspan )
        {
            if ( $rowspan > 1 )
            {
                $sheet->mergeCells ( "J{$currentRow}:J" . ( $currentRow + $rowspan - 1 ) );
                $sheet->getStyle ( "J{$currentRow}:J" . ( $currentRow + $rowspan - 1 ) )->getAlignment ()->setVertical ( 'center' );
            }
            $currentRow += $rowspan;
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
