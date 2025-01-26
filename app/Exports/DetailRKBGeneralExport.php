<?php

namespace App\Exports;

use App\Models\RKB;
use App\Models\DetailRKBGeneral;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;

class DetailRKBGeneralExport implements FromCollection, WithHeadings, WithStyles, WithCustomStartCell, WithEvents
{
    public function startCell () : string
    {
        return 'B2'; // Memulai tabel di cell B2
    }

    public function collection ()
    {
        return DetailRKBGeneral::with ( [ 
            'kategoriSparepart',
            'masterDataSparepart',
            'linkRkbDetails.linkAlatDetailRkb.masterDataAlat',
        ] )
            ->whereHas ( 'kategoriSparepart', function ($query)
            {
                $query->whereIn ( 'nama', [ 
                    'Oil Filter',
                    'Fuel Filter',
                    'Air Filter',
                    'Hydraulic Filter',
                    'Transmission Filter',
                    'Differential Filter',
                ] );
            } )
            ->get ();
    }

    public function headings () : array
    {
        return [ 
            [ 'RENCANA KEBUTUHAN BARANG (RKB) PERALATAN' ],
            [],
            [ 'Nomor RKB', ':' ],
            [ 'Nama Proyek', ':' ],
            [ 'Periode', ':' ],
            [ 'Contact Person', ':' ],
            [],
            [ 
                'NO.',
                'KODE ALAT',
                'JENIS ALAT',
                'TIPE ALAT',
                'UNIT SN',
                'SPAREPART',
                null,
                'P/N',
                'KATEGORI',
                'KEBUTUHAN',
                null,
                null,
                null,
                null,
                'STOK',
                null,
                null,
                null,
                'ALASAN KEBUTUHAN',
                'DISETUJUI',
                null,
                null,
                null,
                null,
                'STATUS ALAT',
                'KETERANGAN',
                'PURCHASING ORDER',
                null,
                null,
                null,
                null,
                'STATUS',
                'PENGIRIMAN BARANG',
                null,
            ],
            [ 
                null,
                null,
                null,
                null,
                null,
                'NAMA',
                'MERK/BRAND',
                null,
                null,
                'QTY',
                'SAT',
                'HARGA (Rp.)',
                'TOTAL (Rp.)',
                'TANGGAL KEBUTUHAN',
                'QTY',
                'SAT',
                'HARGA (Rp.)',
                'TOTAL (Rp.)',
                null,
                'QTY',
                'SAT',
                'HARGA (Rp.)',
                'TOTAL (Rp.)',
                'TANGGAL APPROVAL',
                null,
                null,
                'NAMA SPAREPART',
                'HARGA (Rp.)',
                'NOMOR',
                'TANGGAL',
                'REKANAN',
                null,
                'TANGGAL DIKIRIM',
                'TANGGAL DITERIMA',
            ],
        ];
    }

    public function styles ( Worksheet $sheet )
    {
        $sheet->mergeCells ( 'B2:AI2' );
        $sheet->mergeCells ( 'B9:B10' ); // Merge "NO."
        $sheet->mergeCells ( 'C9:C10' ); // Merge "KODE ALAT"
        $sheet->mergeCells ( 'D9:D10' ); // Merge "JENIS ALAT"
        $sheet->mergeCells ( 'E9:E10' ); // Merge "TIPE ALAT"
        $sheet->mergeCells ( 'F9:F10' ); // Merge "UNIT SN"
        $sheet->mergeCells ( 'G9:H9' ); // Merge "SPAREPART"
        $sheet->mergeCells ( 'I9:I10' ); // Merge "P/N"
        $sheet->mergeCells ( 'J9:J10' ); // Merge "KATEGORI"
        $sheet->mergeCells ( 'K9:O9' ); // Merge "KEBUTUHAN"
        $sheet->mergeCells ( 'P9:S9' ); // Merge "STOK"
        $sheet->mergeCells ( 'T9:T10' ); // Merge "ALASAN KEBUTUHAN"
        $sheet->mergeCells ( 'U9:Y9' ); // Merge "DISETUJUI"
        $sheet->mergeCells ( 'Z9:Z10' ); // Merge "STATUS ALAT"
        $sheet->mergeCells ( 'AA9:AA10' ); // Merge "KETERANGAN"
        $sheet->mergeCells ( 'AB9:AF9' ); // Merge "PURCHASING ORDER"
        $sheet->mergeCells ( 'AG9:AG10' ); // Merge "STATUS"
        $sheet->mergeCells ( 'AH9:AI9' ); // Merge "PENGIRIMAN BARANG"


        $sheet->getStyle ( 'B2' )->getFont ()->setBold ( true )->setSize ( 14 );
        $sheet->getStyle ( 'B2' )->getAlignment ()->setHorizontal ( 'center' );
        $sheet->getStyle ( 'B2' )->getAlignment ()->setVertical ( 'center' );

        $sheet->getStyle ( 'B4:B7' )->getFont ()->setBold ( true );

        $sheet->getStyle ( 'C4:C7' )->getFont ()->setBold ( true );
        $sheet->getStyle ( 'C4:C7' )->getAlignment ()->setHorizontal ( 'right' );
        $sheet->getStyle ( 'C4:C7' )->getAlignment ()->setVertical ( 'center' );

        $sheet->getStyle ( 'D4:D7' )->getAlignment ()->setHorizontal ( 'left' );
        $sheet->getStyle ( 'D4:D7' )->getAlignment ()->setVertical ( 'center' );

        $sheet->getStyle ( 'B9:AI10' )->getFont ()->setBold ( true );
        $sheet->getStyle ( 'B9:AI10' )->getAlignment ()->setHorizontal ( 'center' );
        $sheet->getStyle ( 'B9:AI10' )->getAlignment ()->setVertical ( 'center' );

        return [];
    }

    public function registerEvents () : array
    {
        return [ 
            AfterSheet::class => function (AfterSheet $event)
            {
                $sheet = $event->sheet->getDelegate ();

                // Ambil data dari model RKB
                $rkb = RKB::find ( 1 ); // Sesuaikan dengan ID RKB yang ingin diambil
    
                // Isi data dinamis
                $sheet->setCellValue ( 'D4', $rkb->nomor ?? '-' ); // Nomor RKB
                $sheet->setCellValue ( 'D5', $rkb->proyek->nama ?? '-' ); // Nama Proyek
                $sheet->setCellValue ( 'D6', $rkb->periode->format ( 'F Y' ) ?? '-' ); // Periode
                $sheet->setCellValue ( 'D7', $rkb->contact_person ?? '' ); // Contact Person
    
                // Kosongkan Cell yang Tidak Seharusnya Terisi
                $sheet->setCellValue ( 'F11', '' );
                $sheet->setCellValue ( 'G11', '' );
                $sheet->setCellValue ( 'H11', '' );
                $sheet->setCellValue ( 'I11', '' );
                $sheet->setCellValue ( 'J11', '' );
                $sheet->setCellValue ( 'B12', '' );
                $sheet->setCellValue ( 'C12', '' );
                $sheet->setCellValue ( 'D12', '' );
                $sheet->setCellValue ( 'E12', '' );
                $sheet->setCellValue ( 'F12', '' );
                $sheet->setCellValue ( 'G12', '' );
                $sheet->setCellValue ( 'H12', '' );
                $sheet->setCellValue ( 'I12', '' );
                $sheet->setCellValue ( 'J12', '' );

                // Header dan Kategori
                $headers = [ 
                    [ 
                        'header'     => [ 'code' => 'A', 'name' => 'Pemeliharaan Filter' ],
                        'categories' => [ 
                            [ 'code' => 'B.11', 'name' => 'Oil Filter' ],
                            [ 'code' => 'B.12', 'name' => 'Fuel Filter' ],
                            [ 'code' => 'B.13', 'name' => 'Air Filter' ],
                            [ 'code' => 'B.14', 'name' => 'Hydraulic Filter' ],
                            [ 'code' => 'B.15', 'name' => 'Transmission Filter' ],
                            [ 'code' => 'B.16', 'name' => 'Differential Filter' ],
                        ],
                    ],
                    [ 
                        'header'     => [ 'code' => 'B', 'name' => 'Pemeliharaan Oli & Lubricants' ],
                        'categories' => [ 
                            [ 'code' => 'B.21', 'name' => 'Engine Oil' ],
                            [ 'code' => 'B.22', 'name' => 'Hydraulic Oil' ],
                            [ 'code' => 'B.23', 'name' => 'Transmission Oil' ],
                            [ 'code' => 'B.24', 'name' => 'Final Drive Oil' ],
                            [ 'code' => 'B.25', 'name' => 'Swing & Damper Oil' ],
                            [ 'code' => 'B.26', 'name' => 'Differential Oil' ],
                            [ 'code' => 'B.27', 'name' => 'Grease' ],
                            [ 'code' => 'B.28', 'name' => 'Brake & Power Steering Fluid' ],
                            [ 'code' => 'B.29', 'name' => 'Coolant' ],
                        ],
                    ],
                    [ 
                        'header'     => [ 'code' => 'C', 'name' => 'Pemeliharaan Tyre' ],
                        'categories' => [ 
                            [ 'code' => 'B.3', 'name' => 'Tyre' ],
                        ],
                    ],
                    [ 
                        'header'     => [ 'code' => 'D', 'name' => 'Workshop' ],
                        'categories' => [ 
                            [ 'code' => 'C.1', 'name' => 'Workshop' ],
                        ],
                    ],
                    [ 
                        'header'     => [ 'code' => 'E', 'name' => 'Perbaikan' ],
                        'categories' => [ 
                            [ 'code' => 'A.1', 'name' => 'Cabin' ],
                            [ 'code' => 'A.2', 'name' => 'Engine System' ],
                            [ 'code' => 'A.3', 'name' => 'Transmission System' ],
                            [ 'code' => 'A.4', 'name' => 'Chassis & Swing Machinery' ],
                            [ 'code' => 'A.5', 'name' => 'Differential System' ],
                            [ 'code' => 'A.6', 'name' => 'Electrical System' ],
                            [ 'code' => 'A.7', 'name' => 'Hydraulic / Pneumatic System' ],
                            [ 'code' => 'A.8', 'name' => 'Steering System' ],
                            [ 'code' => 'A.9', 'name' => 'Brake System' ],
                            [ 'code' => 'A.10', 'name' => 'Suspension' ],
                            [ 'code' => 'A.11', 'name' => 'Attachment' ],
                            [ 'code' => 'A.12', 'name' => 'Undercarriage' ],
                            [ 'code' => 'A.13', 'name' => 'Final Drive' ],
                            [ 'code' => 'A.14', 'name' => 'Freight Cost' ],
                        ],
                    ],
                ];

                $row = 11;
                foreach ( $headers as $headerData )
                {
                    $sheet->setCellValue ( "B$row", $headerData[ 'header' ][ 'code' ] );
                    $sheet->setCellValue ( "C$row", $headerData[ 'header' ][ 'name' ] );
                    $sheet->mergeCells ( "C$row:E$row" );
                    $sheet->getStyle ( "B$row:E$row" )->getFont ()->setBold ( true );
                    $row++;

                    foreach ( $headerData[ 'categories' ] as $category )
                    {
                        $sheet->setCellValue ( "B$row", $category[ 'code' ] );
                        $sheet->setCellValue ( "C$row", $category[ 'name' ] );
                        $sheet->mergeCells ( "C$row:E$row" );
                        $sheet->getStyle ( "B$row:E$row" )->applyFromArray ( [ 
                            'font' => [ 
                                'bold' => true,
                            ],
                            'fill' => [ 
                                'fillType'   => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                'startColor' => [ 'argb' => 'FFC0C0C0' ],
                            ],
                        ] );
                        $row++;

                        $data = DetailRKBGeneral::whereHas ( 'kategoriSparepart', function ($query) use ($category)
                        {
                            $query->where ( 'nama', $category[ 'name' ] );
                        } )->get ();

                        $no = 1;
                        foreach ( $data as $item )
                        {
                            $sheet->setCellValue ( "B$row", $no ); // Nomor
                            $sheet->setCellValue ( "C$row", $item->linkRkbDetails->first ()->linkAlatDetailRkb->masterDataAlat->kode_alat ?? '-' );
                            $sheet->setCellValue ( "D$row", $item->linkRkbDetails->first ()->linkAlatDetailRkb->masterDataAlat->jenis_alat ?? '-' );
                            $sheet->setCellValue ( "E$row", $item->linkRkbDetails->first ()->linkAlatDetailRkb->masterDataAlat->tipe_alat ?? '-' );
                            $sheet->setCellValue ( "F$row", $item->linkRkbDetails->first ()->linkAlatDetailRkb->masterDataAlat->serial_number ?? '-' );
                            $sheet->setCellValue ( "G$row", $item->masterDataSparepart->nama ?? '-' );
                            $sheet->setCellValue ( "H$row", $item->masterDataSparepart->merk ?? '-' );
                            $sheet->setCellValue ( "I$row", $item->masterDataSparepart->part_number ?? '-' );
                            $sheet->setCellValue ( "J$row", $item->kategoriSparepart->nama ?? '-' );
                            $sheet->setCellValue ( "K$row", $item->quantity_requested );
                            $sheet->setCellValue ( "L$row", $item->satuan );
                            $row++;
                            $no++;
                        }
                        $row += 2;
                    }
                }

                $highestRow = $sheet->getHighestRow ();
                $sheet->getStyle ( "B9:AI$highestRow" )->applyFromArray ( [ 
                    'borders' => [ 
                        'allBorders' => [ 
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color'       => [ 'argb' => '000000' ],
                        ],
                    ],
                ] );

                // Alignment untuk Kolom Jenis Alat, Tipe Alat, Unit SN
                $sheet->getStyle ( 'D9:L' . $highestRow )->applyFromArray ( [ 
                    'alignment' => [ 
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                ] );

                // Alignment untuk Kolom Jenis Alat, Tipe Alat, Unit SN
                $sheet->getStyle ( 'B11:B' . $highestRow )->applyFromArray ( [ 
                    'alignment' => [ 
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                ] );

                // **Set AutoSize untuk Kolom**
                $sheet->getColumnDimension ( 'B' )->setAutoSize ( true );
                $sheet->getColumnDimension ( 'C' )->setAutoSize ( true );
                $sheet->getColumnDimension ( 'D' )->setAutoSize ( true );
                $sheet->getColumnDimension ( 'E' )->setAutoSize ( true );
                $sheet->getColumnDimension ( 'F' )->setAutoSize ( true );
                $sheet->getColumnDimension ( 'G' )->setAutoSize ( true );
                $sheet->getColumnDimension ( 'H' )->setAutoSize ( true );
                $sheet->getColumnDimension ( 'I' )->setAutoSize ( true );
                $sheet->getColumnDimension ( 'J' )->setAutoSize ( true );
                $sheet->getColumnDimension ( 'K' )->setAutoSize ( true );
                $sheet->getColumnDimension ( 'L' )->setAutoSize ( true );
                $sheet->getColumnDimension ( 'M' )->setAutoSize ( true );
                $sheet->getColumnDimension ( 'N' )->setAutoSize ( true );
                $sheet->getColumnDimension ( 'O' )->setAutoSize ( true );
                $sheet->getColumnDimension ( 'P' )->setAutoSize ( true );
                $sheet->getColumnDimension ( 'Q' )->setAutoSize ( true );
                $sheet->getColumnDimension ( 'R' )->setAutoSize ( true );
                $sheet->getColumnDimension ( 'S' )->setAutoSize ( true );
                $sheet->getColumnDimension ( 'T' )->setAutoSize ( true );
                $sheet->getColumnDimension ( 'U' )->setAutoSize ( true );
                $sheet->getColumnDimension ( 'V' )->setAutoSize ( true );
                $sheet->getColumnDimension ( 'W' )->setAutoSize ( true );
                $sheet->getColumnDimension ( 'X' )->setAutoSize ( true );
                $sheet->getColumnDimension ( 'Y' )->setAutoSize ( true );
                $sheet->getColumnDimension ( 'Z' )->setAutoSize ( true );
                $sheet->getColumnDimension ( 'AA' )->setAutoSize ( true );
                $sheet->getColumnDimension ( 'AB' )->setAutoSize ( true );
                $sheet->getColumnDimension ( 'AC' )->setAutoSize ( true );
                $sheet->getColumnDimension ( 'AD' )->setAutoSize ( true );
                $sheet->getColumnDimension ( 'AE' )->setAutoSize ( true );
                $sheet->getColumnDimension ( 'AF' )->setAutoSize ( true );
                $sheet->getColumnDimension ( 'AG' )->setAutoSize ( true );
                $sheet->getColumnDimension ( 'AH' )->setAutoSize ( true );
                $sheet->getColumnDimension ( 'AI' )->setAutoSize ( true );
            },
        ];
    }
}
