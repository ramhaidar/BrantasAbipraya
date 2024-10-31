<?php

namespace App\Exports;

use App\Models\ATB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Carbon\Carbon;

class ATBExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithCustomStartCell, WithEvents
{
    protected $proyek;
    protected $tipe;
    protected $startDate;
    protected $endDate;

    public function __construct ( $proyek, $tipe,  $startDate = null, $endDate = null)
    {
        $this->proyek = $proyek;
        $this->tipe   = $tipe;
        $this->startDate = $startDate ? Carbon::parse($startDate) : null;
        $this->endDate   = $endDate ? Carbon::parse($endDate) : null;
    }

    public function collection()
    {
        // Buat query dasar
        $query = ATB::with(['komponen', 'masterData'])
                    ->where('id_proyek', $this->proyek)
                    ->where('tipe', $this->tipe);

        // Jika startDate dan endDate diberikan, gunakan filter whereBetween
        if ($this->startDate && $this->endDate) {
            $query->whereBetween('tanggal', [$this->startDate, $this->endDate]);
        }

        // Jika tidak ada filter tanggal, akan menampilkan semua data
        return $query->get();
    }

    public function startCell () : string
    {
        return 'A2'; // Mulai dari sel A2 agar bisa mengatur header secara manual di sel A1
    }

    public function headings(): array
    {
        if ($this->tipe === "Mutasi Proyek") {
            return [
                ['ASAL PROYEK', 'TANGGAL', 'KODE', 'SUPPLIER', 'SPAREPART', 'PART NUMBER', 'QTY', 'SAT', 'NILAI', '', '', '', 'PERBAIKAN', '', '', '', '', '', '', '', '', '', '', '', '', '', 'PEMELIHARAAN', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '','WORKSHOP'],
                ['', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'MAINTENANCE KIT', '', '', '', '', '', 'OIL & LUBRICANTS', '', '', '', '', '', '', '', '', 'TYRE', 'WORKSHOP'],
                ['', '', '', '', '', '', '', '', 'HARGA', 'NET', 'PPN', 'BRUTO', 'CABIN', 'ENGINE SYSTEM', 'TRANSMISSION SYSTEM', 'CHASSIS & SWING MACHINERY', 'DIFFERENTIAL SYSTEM', 'ELECTRICAL SYSTEM', 'HYDRAULIC/PNEUMATIC SYSTEM', 'STEERING SYSTEM', 'BRAKE SYSTEM', 'SUSPENSION', 'ATTACHMENT', 'UNDERCARRIAGE', 'FINAL DRIVE', 'FREIGHT COST', 'OIL FILTER', 'FUEL FILTER', 'AIR FILTER', 'HYDRAULIC FILTER', 'TRANSMISSION FILTER', 'DIFFERENTIAL FILTER', 'ENGINE OIL', 'HYDRAULIC OIL', 'TRANSMISSION OIL', 'FINAL DRIVE OIL', 'SWING & DAMPER OIL', 'DIFFERENTIAL OIL', 'GREASE', 'BRAKE & POWER STEERING FLUID', 'COOLANT', 'TYRE', ''],
                ['', '', '', '', '', '', '', '', '', '', '', '', 'A1', 'A2', 'A3', 'A4', 'A5', 'A6', 'A7', 'A8', 'A9', 'A10', 'A11', 'A12', 'A13', 'A14', 'B11', 'B12', 'B13', 'B14', 'B15', 'B16', 'B21', 'B22', 'B23', 'B24', 'B25', 'B26', 'B27', 'B28', 'B29', 'B3', 'C1']
            ];
        }

        return [
            ['TANGGAL', 'KODE', 'SUPPLIER', 'SPAREPART', 'PART NUMBER', 'QTY', 'SAT', 'NILAI', '', '', '', 'PERBAIKAN', '', '', '', '', '', '', '', '', '', '', '', '', '', 'PEMELIHARAAN', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '','WORKSHOP'],
            ['', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'MAINTENANCE KIT', '', '', '', '', '', 'OIL & LUBRICANTS', '', '', '', '', '', '', '', '', 'TYRE', 'WORKSHOP'],
            ['', '', '', '', '', '', '', 'HARGA', 'NET', 'PPN', 'BRUTO', 'CABIN', 'ENGINE SYSTEM', 'TRANSMISSION SYSTEM', 'CHASSIS & SWING MACHINERY', 'DIFFERENTIAL SYSTEM', 'ELECTRICAL SYSTEM', 'HYDRAULIC/PNEUMATIC SYSTEM', 'STEERING SYSTEM', 'BRAKE SYSTEM', 'SUSPENSION', 'ATTACHMENT', 'UNDERCARRIAGE', 'FINAL DRIVE', 'FREIGHT COST', 'OIL FILTER', 'FUEL FILTER', 'AIR FILTER', 'HYDRAULIC FILTER', 'TRANSMISSION FILTER', 'DIFFERENTIAL FILTER', 'ENGINE OIL', 'HYDRAULIC OIL', 'TRANSMISSION OIL', 'FINAL DRIVE OIL', 'SWING & DAMPER OIL', 'DIFFERENTIAL OIL', 'GREASE', 'BRAKE & POWER STEERING FLUID', 'COOLANT', 'TYRE', ''],
            ['', '', '', '', '', '', '', '', '', '', '', 'A1', 'A2', 'A3', 'A4', 'A5', 'A6', 'A7', 'A8', 'A9', 'A10', 'A11', 'A12', 'A13', 'A14', 'B11', 'B12', 'B13', 'B14', 'B15', 'B16', 'B21', 'B22', 'B23', 'B24', 'B25', 'B26', 'B27', 'B28', 'B29', 'B3', 'C1']
        ];
    }

    public function map($row): array
    {
        $kode          = $row->komponen ? $row->komponen->kode : '';
        $formattedDate = Carbon::parse($row->tanggal)->isoFormat('D MMMM YYYY');

        // Ambil data dari relasi masterData
        $supplier    = $row->masterData ? $row->masterData->supplier : '';
        $sparepart   = $row->masterData ? $row->masterData->sparepart : '';
        $part_number = $row->masterData ? $row->masterData->part_number : '';

        // Mapping kode perbaikan
        $perbaikan = [
            'A1'  => 0,
            'A2'  => 0,
            'A3'  => 0,
            'A4'  => 0,
            'A5'  => 0,
            'A6'  => 0,
            'A7'  => 0,
            'A8'  => 0,
            'A9'  => 0,
            'A10' => 0,
            'A11' => 0,
            'A12' => 0,
            'A13' => 0,
            'A14' => 0,
        ];

        // Mapping kode pemeliharaan
        $pemeliharaan = [
            'B11' => 0,
            'B12' => 0,
            'B13' => 0,
            'B14' => 0,
            'B15' => 0,
            'B16' => 0,
            'B21' => 0,
            'B22' => 0,
            'B23' => 0,
            'B24' => 0,
            'B25' => 0,
            'B26' => 0,
            'B27' => 0,
            'B28' => 0,
            'B29' => 0,
            'B3'  => 0,
        ];

        // Mapping kode workshop
        $workshop = [
            'C1' => 0,
        ];

        // Assign values based on group name
        if ($row->komponen && $row->komponen->first_group) {
            if ($row->komponen->first_group->name == 'PERBAIKAN') {
                $perbaikan[$row->komponen->kode] = $row->net;
            } elseif ($row->komponen->first_group->name == 'PEMELIHARAAN') {
                $pemeliharaan[$row->komponen->kode] = $row->net;
            } elseif ($row->komponen->first_group->name == 'WAREHOUSE') {
                $workshop[$row->komponen->kode] = $row->net;
            }
        }

        // Jika tipe ATB adalah Mutasi Proyek, tambahkan kolom Asal Proyek
        if ($this->tipe === "Mutasi Proyek") {
            return [
                $row->asal_proyek,  // Asal Proyek untuk Mutasi Proyek
                $formattedDate,
                $kode,
                $supplier,
                $sparepart,
                $part_number,
                $row->quantity,
                $row->satuan,
                $row->harga,
                $row->net,
                $row->ppn,
                $row->bruto,
                $perbaikan['A1'],
                $perbaikan['A2'],
                $perbaikan['A3'],
                $perbaikan['A4'],
                $perbaikan['A5'],
                $perbaikan['A6'],
                $perbaikan['A7'],
                $perbaikan['A8'],
                $perbaikan['A9'],
                $perbaikan['A10'],
                $perbaikan['A11'],
                $perbaikan['A12'],
                $perbaikan['A13'],
                $perbaikan['A14'],
                $pemeliharaan['B11'],
                $pemeliharaan['B12'],
                $pemeliharaan['B13'],
                $pemeliharaan['B14'],
                $pemeliharaan['B15'],
                $pemeliharaan['B16'],
                $pemeliharaan['B21'],
                $pemeliharaan['B22'],
                $pemeliharaan['B23'],
                $pemeliharaan['B24'],
                $pemeliharaan['B25'],
                $pemeliharaan['B26'],
                $pemeliharaan['B27'],
                $pemeliharaan['B28'],
                $pemeliharaan['B29'],
                $pemeliharaan['B3'],
                $workshop['C1'],  // Workshop kode C1
            ];
        }

        // Jika bukan Mutasi Proyek, hilangkan kolom 'asal_proyek'
        return [
            $formattedDate,
            $kode,
            $supplier,
            $sparepart,
            $part_number,
            $row->quantity,
            $row->satuan,
            $row->harga,
            $row->net,
            $row->ppn,
            $row->bruto,
            $perbaikan['A1'],
            $perbaikan['A2'],
            $perbaikan['A3'],
            $perbaikan['A4'],
            $perbaikan['A5'],
            $perbaikan['A6'],
            $perbaikan['A7'],
            $perbaikan['A8'],
            $perbaikan['A9'],
            $perbaikan['A10'],
            $perbaikan['A11'],
            $perbaikan['A12'],
            $perbaikan['A13'],
            $perbaikan['A14'],
            $pemeliharaan['B11'],
            $pemeliharaan['B12'],
            $pemeliharaan['B13'],
            $pemeliharaan['B14'],
            $pemeliharaan['B15'],
            $pemeliharaan['B16'],
            $pemeliharaan['B21'],
            $pemeliharaan['B22'],
            $pemeliharaan['B23'],
            $pemeliharaan['B24'],
            $pemeliharaan['B25'],
            $pemeliharaan['B26'],
            $pemeliharaan['B27'],
            $pemeliharaan['B28'],
            $pemeliharaan['B29'],
            $pemeliharaan['B3'],
            $workshop['C1'],  // Workshop kode C1 tetap dimasukkan meskipun bukan Mutasi Proyek
        ];

        // Hilangkan 'tipe' dari hasil return jika tidak ingin ditampilkan
        return array_slice($data, 1);
    }


    public function styles(Worksheet $sheet)
    {
        // Mendapatkan baris terakhir
        $highestRow = $sheet->getHighestRow();

        // Merging cells for the headers
        if ($this->tipe === 'Mutasi Proyek') {
            // Merge for Asal Proyek
            $sheet->mergeCells('A2:A4'); // Merge for ASAL PROYEK
            $sheet->mergeCells('B2:B4'); // Merge for TANGGAL
            $sheet->mergeCells('C2:C4'); // Merge for KODE
            $sheet->mergeCells('D2:D4'); // Merge for SUPPLIER
            $sheet->mergeCells('E2:E4'); // Merge for SPAREPART
            $sheet->mergeCells('F2:F4'); // Merge for PART NUMBER
            $sheet->mergeCells('G2:G4'); // Merge for QTY
            $sheet->mergeCells('H2:H4'); // Merge for SAT
            $sheet->mergeCells('I2:L3'); // Merge for NILAI
            $sheet->mergeCells('M2:Z3'); // Merge for PERBAIKAN
            $sheet->mergeCells('AA2:AP2'); // Merge for PEMELIHARAAN
            $sheet->mergeCells('AA3:AF3'); // Merge for MAINTENANCE KIT
            $sheet->mergeCells('AG3:AO3'); // Merge for OIL & LUBRICANTS
            $sheet->mergeCells('AP3:AP4'); // Merge for TYRE
            $sheet->mergeCells('AQ2:AQ4'); // Merge for WORKSHOP
        } else {
            // Merging cells when Asal Proyek is not included
            $sheet->mergeCells('A2:A4'); // Merge for TANGGAL
            $sheet->mergeCells('B2:B4'); // Merge for KODE
            $sheet->mergeCells('C2:C4'); // Merge for SUPPLIER
            $sheet->mergeCells('D2:D4'); // Merge for SPAREPART
            $sheet->mergeCells('E2:E4'); // Merge for PART NUMBER
            $sheet->mergeCells('F2:F4'); // Merge for QTY
            $sheet->mergeCells('G2:G4'); // Merge for SAT
            $sheet->mergeCells('H2:K3'); // Merge for NILAI
            $sheet->mergeCells('L2:Y3'); // Merge for PERBAIKAN
            $sheet->mergeCells('Z2:AO2'); // Merge for PEMELIHARAAN
            $sheet->mergeCells('Z3:AE3'); // Merge for MAINTENANCE KIT
            $sheet->mergeCells('AF3:AN3'); // Merge for OIL & LUBRICANTS
            $sheet->mergeCells('AO3:AO4'); // Merge for TYRE
            $sheet->mergeCells('AP2:AP4'); // Merge for WORKSHOP
        }

        if ($this->tipe === 'Mutasi Proyek') {
            // Styling headers untuk tipe "Mutasi Proyek"
            $sheet->getStyle('A2:AQ5')->getFont()->setBold(true);
            $sheet->getStyle('A2:AQ5')->getAlignment()->setHorizontal('center');
            $sheet->getStyle('A2:AQ5')->getAlignment()->setVertical('center');
            $sheet->getStyle('A2:AQ5')->getAlignment()->setWrapText(true);
        } else {
            // Styling headers untuk tipe lainnya
            $sheet->getStyle('A2:AP5')->getFont()->setBold(true);
            $sheet->getStyle('A2:AP5')->getAlignment()->setHorizontal('center');
            $sheet->getStyle('A2:AP5')->getAlignment()->setVertical('center');
            $sheet->getStyle('A2:AP5')->getAlignment()->setWrapText(true);
        }

        if ($this->tipe === 'Mutasi Proyek') {
            // Centering the content in the body untuk tipe "Mutasi Proyek"
            $highestRow = $sheet->getHighestRow();
            $sheet->getStyle('B2:B' . $highestRow)->getAlignment()->setHorizontal('center');
            $sheet->getStyle('B2:B' . $highestRow)->getAlignment()->setVertical('center');
            $sheet->getStyle('C2:C' . $highestRow)->getAlignment()->setHorizontal('center');
            $sheet->getStyle('C2:C' . $highestRow)->getAlignment()->setVertical('center');
            $sheet->getStyle('G2:G' . $highestRow)->getAlignment()->setHorizontal('center');
            $sheet->getStyle('G2:G' . $highestRow)->getAlignment()->setVertical('center');
            $sheet->getStyle('H2:H' . $highestRow)->getAlignment()->setHorizontal('center');
            $sheet->getStyle('H2:H' . $highestRow)->getAlignment()->setVertical('center');
        } else {
            // Centering the content in the body
            $highestRow = $sheet->getHighestRow();
            $sheet->getStyle('A2:A' . $highestRow)->getAlignment()->setHorizontal('center');
            $sheet->getStyle('A2:A' . $highestRow)->getAlignment()->setVertical('center');
            $sheet->getStyle('B2:B' . $highestRow)->getAlignment()->setHorizontal('center');
            $sheet->getStyle('B2:B' . $highestRow)->getAlignment()->setVertical('center');
            $sheet->getStyle('F2:F' . $highestRow)->getAlignment()->setHorizontal('center');
            $sheet->getStyle('F2:F' . $highestRow)->getAlignment()->setVertical('center');
            $sheet->getStyle('G2:G' . $highestRow)->getAlignment()->setHorizontal('center');
            $sheet->getStyle('G2:G' . $highestRow)->getAlignment()->setVertical('center');   
        }

        // Set borders for headers (menyesuaikan jika tipe ATB "Mutasi Proyek")
        if ($this->tipe === 'Mutasi Proyek') {
            $sheet->getStyle('A2:AQ4')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        } else {
            $sheet->getStyle('A2:AP4')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        }

        // Set borders for the entire table
        if ($this->tipe === 'Mutasi Proyek') {
            $sheet->getStyle('A2:AQ' . $highestRow)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        } else {
            $sheet->getStyle('A2:AP' . $highestRow)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        }

        if ($this->tipe === "Mutasi Proyek") {
            // Styling untuk Mutasi Proyek (background color mulai dari kolom M)
            $sheet->getStyle('M2:AQ' . $highestRow)->applyFromArray([
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'color'    => ['rgb' => 'C0C0C0'] // Warna background yang dimulai dari kolom M
                ]
            ]);
        } else {
            // Styling untuk tipe lain (background color mulai dari kolom L)
            $sheet->getStyle('L2:AP' . $highestRow)->applyFromArray([
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'color'    => ['rgb' => 'C0C0C0'] // Warna background tipe non-Mutasi Proyek
                ]
            ]);
        }

        if ($this->tipe === 'Mutasi Proyek') {
            // Set column widths tipe "Mutasi Proyek"
            $sheet->getColumnDimension('A')->setAutoSize(true);
            $sheet->getColumnDimension('B')->setAutoSize(true);
            $sheet->getColumnDimension('C')->setAutoSize(true);
            $sheet->getColumnDimension('D')->setAutoSize(true);
            $sheet->getColumnDimension('E')->setAutoSize(true);
            $sheet->getColumnDimension('F')->setAutoSize(true);
            $sheet->getColumnDimension('G')->setAutoSize(true);
            $sheet->getColumnDimension('H')->setAutoSize(true);
            $sheet->getColumnDimension('I')->setAutoSize(true);
            $sheet->getColumnDimension('J')->setAutoSize(true);
            $sheet->getColumnDimension('K')->setAutoSize(true);
            $sheet->getColumnDimension('L')->setAutoSize(true);
            $sheet->getColumnDimension('M')->setAutoSize(true);
            $sheet->getColumnDimension('N')->setAutoSize(true);
            $sheet->getColumnDimension('O')->setAutoSize(true);
            $sheet->getColumnDimension('P')->setAutoSize(true);
            $sheet->getColumnDimension('Q')->setAutoSize(true);
            $sheet->getColumnDimension('R')->setAutoSize(true);
            $sheet->getColumnDimension('S')->setAutoSize(true);
            $sheet->getColumnDimension('T')->setAutoSize(true);
            $sheet->getColumnDimension('U')->setAutoSize(true);
            $sheet->getColumnDimension('V')->setAutoSize(true);
            $sheet->getColumnDimension('W')->setAutoSize(true);
            $sheet->getColumnDimension('X')->setAutoSize(true);
            $sheet->getColumnDimension('Y')->setAutoSize(true);
            $sheet->getColumnDimension('Z')->setAutoSize(true);
            $sheet->getColumnDimension('AA')->setAutoSize(true);
            $sheet->getColumnDimension('AB')->setAutoSize(true);
            $sheet->getColumnDimension('AC')->setAutoSize(true);
            $sheet->getColumnDimension('AD')->setAutoSize(true);
            $sheet->getColumnDimension('AE')->setAutoSize(true);
            $sheet->getColumnDimension('AF')->setAutoSize(true);
            $sheet->getColumnDimension('AG')->setAutoSize(true);
            $sheet->getColumnDimension('AH')->setAutoSize(true);
            $sheet->getColumnDimension('AI')->setAutoSize(true);
            $sheet->getColumnDimension('AJ')->setAutoSize(true);
            $sheet->getColumnDimension('AK')->setAutoSize(true);
            $sheet->getColumnDimension('AL')->setAutoSize(true);
            $sheet->getColumnDimension('AM')->setAutoSize(true);
            $sheet->getColumnDimension('AN')->setAutoSize(true);
            $sheet->getColumnDimension('AO')->setAutoSize(true);
            $sheet->getColumnDimension('AP')->setAutoSize(true);
            $sheet->getColumnDimension('AQ')->setAutoSize(true);
        } else {
            // Set column widths
            $sheet->getColumnDimension('A')->setAutoSize(true);
            $sheet->getColumnDimension('B')->setAutoSize(true);
            $sheet->getColumnDimension('C')->setAutoSize(true);
            $sheet->getColumnDimension('D')->setAutoSize(true);
            $sheet->getColumnDimension('E')->setAutoSize(true);
            $sheet->getColumnDimension('F')->setAutoSize(true);
            $sheet->getColumnDimension('G')->setAutoSize(true);
            $sheet->getColumnDimension('H')->setAutoSize(true);
            $sheet->getColumnDimension('I')->setAutoSize(true);
            $sheet->getColumnDimension('J')->setAutoSize(true);
            $sheet->getColumnDimension('K')->setAutoSize(true);
            $sheet->getColumnDimension('L')->setAutoSize(true);
            $sheet->getColumnDimension('M')->setAutoSize(true);
            $sheet->getColumnDimension('N')->setAutoSize(true);
            $sheet->getColumnDimension('O')->setAutoSize(true);
            $sheet->getColumnDimension('P')->setAutoSize(true);
            $sheet->getColumnDimension('Q')->setAutoSize(true);
            $sheet->getColumnDimension('R')->setAutoSize(true);
            $sheet->getColumnDimension('S')->setAutoSize(true);
            $sheet->getColumnDimension('T')->setAutoSize(true);
            $sheet->getColumnDimension('U')->setAutoSize(true);
            $sheet->getColumnDimension('V')->setAutoSize(true);
            $sheet->getColumnDimension('W')->setAutoSize(true);
            $sheet->getColumnDimension('X')->setAutoSize(true);
            $sheet->getColumnDimension('Y')->setAutoSize(true);
            $sheet->getColumnDimension('Z')->setAutoSize(true);
            $sheet->getColumnDimension('AA')->setAutoSize(true);
            $sheet->getColumnDimension('AB')->setAutoSize(true);
            $sheet->getColumnDimension('AC')->setAutoSize(true);
            $sheet->getColumnDimension('AD')->setAutoSize(true);
            $sheet->getColumnDimension('AE')->setAutoSize(true);
            $sheet->getColumnDimension('AF')->setAutoSize(true);
            $sheet->getColumnDimension('AG')->setAutoSize(true);
            $sheet->getColumnDimension('AH')->setAutoSize(true);
            $sheet->getColumnDimension('AI')->setAutoSize(true);
            $sheet->getColumnDimension('AJ')->setAutoSize(true);
            $sheet->getColumnDimension('AK')->setAutoSize(true);
            $sheet->getColumnDimension('AL')->setAutoSize(true);
            $sheet->getColumnDimension('AM')->setAutoSize(true);
            $sheet->getColumnDimension('AN')->setAutoSize(true);
            $sheet->getColumnDimension('AO')->setAutoSize(true);
            $sheet->getColumnDimension('AP')->setAutoSize(true);
        }
        
        return [
            2 => ['font' => ['bold' => true]],
            3 => ['font' => ['bold' => true]],
        ];
    }


    public function registerEvents () : array
    {
        return [ 
            AfterSheet::class => function (AfterSheet $event)
            {
                $sheet      = $event->sheet->getDelegate ();
                $highestRow = $sheet->getHighestRow ();

                if ($this->tipe === "Mutasi Proyek") {
                    $totalRow = $highestRow + 1;
                    $sheet->mergeCells ( 'A' . $totalRow . ':H' . $totalRow );
                    $sheet->setCellValue ( 'A' . $totalRow, 'Total' );
                    $sheet->setCellValue ( 'I' . $totalRow, '=SUM(I6:I' . $highestRow . ')' );
                    $sheet->setCellValue ( 'J' . $totalRow, '=SUM(J6:J' . $highestRow . ')' );
                    $sheet->setCellValue ( 'K' . $totalRow, '=SUM(K6:K' . $highestRow . ')' );
                    $sheet->setCellValue ( 'L' . $totalRow, '=SUM(L6:L' . $highestRow . ')' );
                    $sheet->setCellValue ( 'M' . $totalRow, '=SUM(M6:M' . $highestRow . ')' );
                    $sheet->setCellValue ( 'N' . $totalRow, '=SUM(N6:N' . $highestRow . ')' );
                    $sheet->setCellValue ( 'O' . $totalRow, '=SUM(O6:O' . $highestRow . ')' );
                    $sheet->setCellValue ( 'P' . $totalRow, '=SUM(P6:P' . $highestRow . ')' );
                    $sheet->setCellValue ( 'Q' . $totalRow, '=SUM(Q6:Q' . $highestRow . ')' );
                    $sheet->setCellValue ( 'R' . $totalRow, '=SUM(R6:R' . $highestRow . ')' );
                    $sheet->setCellValue ( 'S' . $totalRow, '=SUM(S6:S' . $highestRow . ')' );
                    $sheet->setCellValue ( 'T' . $totalRow, '=SUM(T6:T' . $highestRow . ')' );
                    $sheet->setCellValue ( 'U' . $totalRow, '=SUM(U6:U' . $highestRow . ')' );
                    $sheet->setCellValue ( 'V' . $totalRow, '=SUM(V6:V' . $highestRow . ')' );
                    $sheet->setCellValue ( 'W' . $totalRow, '=SUM(W6:W' . $highestRow . ')' );
                    $sheet->setCellValue ( 'X' . $totalRow, '=SUM(X6:X' . $highestRow . ')' );
                    $sheet->setCellValue ( 'Y' . $totalRow, '=SUM(Y6:Y' . $highestRow . ')' );
                    $sheet->setCellValue ( 'Z' . $totalRow, '=SUM(Z6:Z' . $highestRow . ')' );
                    $sheet->setCellValue ( 'AA' . $totalRow, '=SUM(AA6:AA' . $highestRow . ')' );
                    $sheet->setCellValue ( 'AB' . $totalRow, '=SUM(AB6:AB' . $highestRow . ')' );
                    $sheet->setCellValue ( 'AC' . $totalRow, '=SUM(AC6:AC' . $highestRow . ')' );
                    $sheet->setCellValue ( 'AD' . $totalRow, '=SUM(AD6:AD' . $highestRow . ')' );
                    $sheet->setCellValue ( 'AE' . $totalRow, '=SUM(AE6:AE' . $highestRow . ')' );
                    $sheet->setCellValue ( 'AF' . $totalRow, '=SUM(AF6:AF' . $highestRow . ')' );
                    $sheet->setCellValue ( 'AG' . $totalRow, '=SUM(AG6:AG' . $highestRow . ')' );
                    $sheet->setCellValue ( 'AH' . $totalRow, '=SUM(AH6:AH' . $highestRow . ')' );
                    $sheet->setCellValue ( 'AI' . $totalRow, '=SUM(AI6:AI' . $highestRow . ')' );
                    $sheet->setCellValue ( 'AJ' . $totalRow, '=SUM(AJ6:AJ' . $highestRow . ')' );
                    $sheet->setCellValue ( 'AK' . $totalRow, '=SUM(AK6:AK' . $highestRow . ')' );
                    $sheet->setCellValue ( 'AL' . $totalRow, '=SUM(AL6:AL' . $highestRow . ')' );
                    $sheet->setCellValue ( 'AM' . $totalRow, '=SUM(AM6:AM' . $highestRow . ')' );
                    $sheet->setCellValue ( 'AN' . $totalRow, '=SUM(AN6:AN' . $highestRow . ')' );
                    $sheet->setCellValue ( 'AO' . $totalRow, '=SUM(AO6:AO' . $highestRow . ')' );
                    $sheet->setCellValue ( 'AP' . $totalRow, '=SUM(AP6:AP' . $highestRow . ')' );
                    $sheet->setCellValue ( 'AQ' . $totalRow, '=SUM(AQ6:AQ' . $highestRow . ')' );
                } else{
                    // Menambahkan baris total
                    $totalRow = $highestRow + 1;
                    $sheet->mergeCells ( 'A' . $totalRow . ':G' . $totalRow );
                    $sheet->setCellValue ( 'A' . $totalRow, 'Total' );
                    $sheet->setCellValue ( 'H' . $totalRow, '=SUM(H6:H' . $highestRow . ')' );
                    $sheet->setCellValue ( 'I' . $totalRow, '=SUM(I6:I' . $highestRow . ')' );
                    $sheet->setCellValue ( 'J' . $totalRow, '=SUM(J6:J' . $highestRow . ')' );
                    $sheet->setCellValue ( 'K' . $totalRow, '=SUM(K6:K' . $highestRow . ')' );
                    $sheet->setCellValue ( 'L' . $totalRow, '=SUM(L6:L' . ( $highestRow ) . ')' );
                    $sheet->setCellValue ( 'M' . $totalRow, '=SUM(M6:M' . ( $highestRow ) . ')' );
                    $sheet->setCellValue ( 'N' . $totalRow, '=SUM(N6:N' . ( $highestRow ) . ')' );
                    $sheet->setCellValue ( 'O' . $totalRow, '=SUM(O6:O' . ( $highestRow ) . ')' );
                    $sheet->setCellValue ( 'P' . $totalRow, '=SUM(P6:P' . ( $highestRow ) . ')' );
                    $sheet->setCellValue ( 'Q' . $totalRow, '=SUM(Q6:Q' . ( $highestRow ) . ')' );
                    $sheet->setCellValue ( 'R' . $totalRow, '=SUM(R6:R' . ( $highestRow ) . ')' );
                    $sheet->setCellValue ( 'S' . $totalRow, '=SUM(S6:S' . ( $highestRow ) . ')' );
                    $sheet->setCellValue ( 'T' . $totalRow, '=SUM(T6:T' . ( $highestRow ) . ')' );
                    $sheet->setCellValue ( 'U' . $totalRow, '=SUM(U6:U' . ( $highestRow ) . ')' );
                    $sheet->setCellValue ( 'V' . $totalRow, '=SUM(V6:V' . ( $highestRow ) . ')' );
                    $sheet->setCellValue ( 'W' . $totalRow, '=SUM(W6:W' . ( $highestRow ) . ')' );
                    $sheet->setCellValue ( 'X' . $totalRow, '=SUM(X6:X' . ( $highestRow ) . ')' );
                    $sheet->setCellValue ( 'Y' . $totalRow, '=SUM(Y6:Y' . ( $highestRow ) . ')' );
                    $sheet->setCellValue ( 'Z' . $totalRow, '=SUM(Z6:Z' . ( $highestRow ) . ')' );
                    $sheet->setCellValue ( 'AA' . $totalRow, '=SUM(AA6:AA' . ( $highestRow ) . ')' );
                    $sheet->setCellValue ( 'AB' . $totalRow, '=SUM(AB6:AB' . ( $highestRow ) . ')' );
                    $sheet->setCellValue ( 'AC' . $totalRow, '=SUM(AC6:AC' . ( $highestRow ) . ')' );
                    $sheet->setCellValue ( 'AD' . $totalRow, '=SUM(AD6:AD' . ( $highestRow ) . ')' );
                    $sheet->setCellValue ( 'AE' . $totalRow, '=SUM(AE6:AE' . ( $highestRow ) . ')' );
                    $sheet->setCellValue ( 'AF' . $totalRow, '=SUM(AF6:AF' . ( $highestRow ) . ')' );
                    $sheet->setCellValue ( 'AG' . $totalRow, '=SUM(AG6:AG' . ( $highestRow ) . ')' );
                    $sheet->setCellValue ( 'AH' . $totalRow, '=SUM(AH6:AH' . ( $highestRow ) . ')' );
                    $sheet->setCellValue ( 'AI' . $totalRow, '=SUM(AI6:AI' . ( $highestRow ) . ')' );
                    $sheet->setCellValue ( 'AJ' . $totalRow, '=SUM(AJ6:AJ' . ( $highestRow ) . ')' );
                    $sheet->setCellValue ( 'AK' . $totalRow, '=SUM(AK6:AK' . ( $highestRow ) . ')' );
                    $sheet->setCellValue ( 'AL' . $totalRow, '=SUM(AL6:AL' . ( $highestRow ) . ')' );
                    $sheet->setCellValue ( 'AM' . $totalRow, '=SUM(AM6:AM' . ( $highestRow ) . ')' );
                    $sheet->setCellValue ( 'AN' . $totalRow, '=SUM(AN6:AN' . ( $highestRow ) . ')' );
                    $sheet->setCellValue ( 'AO' . $totalRow, '=SUM(AO6:AO' . ( $highestRow ) . ')' );
                    $sheet->setCellValue ( 'AP' . $totalRow, '=SUM(AP6:AP' . ( $highestRow ) . ')' );
                }

                if ($this->tipe === "Mutasi Proyek") {
                    // Styling total row
                    $sheet->getStyle ( 'A' . $totalRow . ':L' . $totalRow )->getFont ()->setBold ( true );
                    $sheet->getStyle ( 'A' . $totalRow . ':L' . $totalRow )->getAlignment ()->setHorizontal ( 'center' );
                    $sheet->getStyle ( 'A' . $totalRow . ':L' . $totalRow )->getAlignment ()->setVertical ( 'center' );
                    $sheet->getStyle ( 'A' . $totalRow . ':L' . $totalRow )->getBorders ()->getAllBorders ()->setBorderStyle ( \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN );
                    $sheet->getStyle ( 'L' . $totalRow . ':AQ' . $totalRow )->getFont ()->setBold ( true );
                    $sheet->getStyle ( 'L' . $totalRow . ':AQ' . $totalRow )->getAlignment ()->setHorizontal ( 'center' );
                    $sheet->getStyle ( 'L' . $totalRow . ':AQ' . $totalRow )->getAlignment ()->setVertical ( 'center' );
                    $sheet->getStyle ( 'L' . $totalRow . ':AQ' . $totalRow )->getBorders ()->getAllBorders ()->setBorderStyle ( \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN );
                } else {
                    // Styling total row
                    $sheet->getStyle ( 'A' . $totalRow . ':K' . $totalRow )->getFont ()->setBold ( true );
                    $sheet->getStyle ( 'A' . $totalRow . ':K' . $totalRow )->getAlignment ()->setHorizontal ( 'center' );
                    $sheet->getStyle ( 'A' . $totalRow . ':K' . $totalRow )->getAlignment ()->setVertical ( 'center' );
                    $sheet->getStyle ( 'A' . $totalRow . ':K' . $totalRow )->getBorders ()->getAllBorders ()->setBorderStyle ( \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN );
                    $sheet->getStyle ( 'H' . $totalRow . ':AP' . $totalRow )->getFont ()->setBold ( true );
                    $sheet->getStyle ( 'H' . $totalRow . ':AP' . $totalRow )->getAlignment ()->setHorizontal ( 'center' );
                    $sheet->getStyle ( 'H' . $totalRow . ':AP' . $totalRow )->getAlignment ()->setVertical ( 'center' );
                    $sheet->getStyle ( 'H' . $totalRow . ':AP' . $totalRow )->getBorders ()->getAllBorders ()->setBorderStyle ( \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN );
                }
                
                if ($this->tipe === "Mutasi Proyek") {
                    // Styling untuk Mutasi Proyek (sampai kolom AQ)
                    $sheet->getStyle('A' . $totalRow . ':AQ' . $totalRow)->applyFromArray([
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'color'    => ['rgb' => 'FFFFCC'] // Warna background baris total
                        ]
                    ]);
                } else {
                    // Styling untuk tipe lain (sampai kolom AP)
                    $sheet->getStyle('A' . $totalRow . ':AP' . $totalRow)->applyFromArray([
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'color'    => ['rgb' => 'FFFFCC'] // Warna background baris total
                        ]
                    ]);
                }

                if ($this->tipe === "Mutasi Proyek") {
                    $sheet->getStyle ( 'H6:H' . $totalRow )->getNumberFormat ()->setFormatCode ( '#,##0' );
                    $sheet->getStyle ( 'I6:I' . $totalRow )->getNumberFormat ()->setFormatCode ( '#,##0' );
                    $sheet->getStyle ( 'J6:J' . $totalRow )->getNumberFormat ()->setFormatCode ( '#,##0' );
                    $sheet->getStyle ( 'K6:K' . $totalRow )->getNumberFormat ()->setFormatCode ( '#,##0' );
                    $sheet->getStyle ( 'L6:L' . $totalRow )->getNumberFormat ()->setFormatCode ( '#,##0' );
                    $sheet->getStyle ( 'M6:M' . $totalRow )->getNumberFormat ()->setFormatCode ( '#,##0' );
                    $sheet->getStyle ( 'N6:N' . $totalRow )->getNumberFormat ()->setFormatCode ( '#,##0' );
                    $sheet->getStyle ( 'O6:O' . $totalRow )->getNumberFormat ()->setFormatCode ( '#,##0' );
                    $sheet->getStyle ( 'P6:P' . $totalRow )->getNumberFormat ()->setFormatCode ( '#,##0' );
                    $sheet->getStyle ( 'Q6:Q' . $totalRow )->getNumberFormat ()->setFormatCode ( '#,##0' );
                    $sheet->getStyle ( 'R6:R' . $totalRow )->getNumberFormat ()->setFormatCode ( '#,##0' );
                    $sheet->getStyle ( 'S6:S' . $totalRow )->getNumberFormat ()->setFormatCode ( '#,##0' );
                    $sheet->getStyle ( 'T6:T' . $totalRow )->getNumberFormat ()->setFormatCode ( '#,##0' );
                    $sheet->getStyle ( 'U6:U' . $totalRow )->getNumberFormat ()->setFormatCode ( '#,##0' );
                    $sheet->getStyle ( 'V6:V' . $totalRow )->getNumberFormat ()->setFormatCode ( '#,##0' );
                    $sheet->getStyle ( 'W6:W' . $totalRow )->getNumberFormat ()->setFormatCode ( '#,##0' );
                    $sheet->getStyle ( 'X6:X' . $totalRow )->getNumberFormat ()->setFormatCode ( '#,##0' );
                    $sheet->getStyle ( 'Y6:Y' . $totalRow )->getNumberFormat ()->setFormatCode ( '#,##0' );
                    $sheet->getStyle ( 'Z6:Z' . $totalRow )->getNumberFormat ()->setFormatCode ( '#,##0' );
                    $sheet->getStyle ( 'AA6:AA' . $totalRow )->getNumberFormat ()->setFormatCode ( '#,##0' );
                    $sheet->getStyle ( 'AB6:AB' . $totalRow )->getNumberFormat ()->setFormatCode ( '#,##0' );
                    $sheet->getStyle ( 'AC6:AC' . $totalRow )->getNumberFormat ()->setFormatCode ( '#,##0' );
                    $sheet->getStyle ( 'AD6:AD' . $totalRow )->getNumberFormat ()->setFormatCode ( '#,##0' );
                    $sheet->getStyle ( 'AE6:AE' . $totalRow )->getNumberFormat ()->setFormatCode ( '#,##0' );
                    $sheet->getStyle ( 'AF6:AF' . $totalRow )->getNumberFormat ()->setFormatCode ( '#,##0' );
                    $sheet->getStyle ( 'AG6:AG' . $totalRow )->getNumberFormat ()->setFormatCode ( '#,##0' );
                    $sheet->getStyle ( 'AH6:AH' . $totalRow )->getNumberFormat ()->setFormatCode ( '#,##0' );
                    $sheet->getStyle ( 'AI6:AI' . $totalRow )->getNumberFormat ()->setFormatCode ( '#,##0' );
                    $sheet->getStyle ( 'AJ6:AJ' . $totalRow )->getNumberFormat ()->setFormatCode ( '#,##0' );
                    $sheet->getStyle ( 'AK6:AK' . $totalRow )->getNumberFormat ()->setFormatCode ( '#,##0' );
                    $sheet->getStyle ( 'AL6:AL' . $totalRow )->getNumberFormat ()->setFormatCode ( '#,##0' );
                    $sheet->getStyle ( 'AM6:AM' . $totalRow )->getNumberFormat ()->setFormatCode ( '#,##0' );
                    $sheet->getStyle ( 'AN6:AN' . $totalRow )->getNumberFormat ()->setFormatCode ( '#,##0' );
                    $sheet->getStyle ( 'AO6:AO' . $totalRow )->getNumberFormat ()->setFormatCode ( '#,##0' );
                    $sheet->getStyle ( 'AP6:AP' . $totalRow )->getNumberFormat ()->setFormatCode ( '#,##0' );
                    $sheet->getStyle ( 'AQ6:AQ' . $totalRow )->getNumberFormat ()->setFormatCode ( '#,##0' );
                } else {
                    $sheet->getStyle ( 'H6:H' . $totalRow )->getNumberFormat ()->setFormatCode ( '#,##0' );
                    $sheet->getStyle ( 'I6:I' . $totalRow )->getNumberFormat ()->setFormatCode ( '#,##0' );
                    $sheet->getStyle ( 'J6:J' . $totalRow )->getNumberFormat ()->setFormatCode ( '#,##0' );
                    $sheet->getStyle ( 'K6:K' . $totalRow )->getNumberFormat ()->setFormatCode ( '#,##0' );
                    $sheet->getStyle ( 'L6:L' . $totalRow )->getNumberFormat ()->setFormatCode ( '#,##0' );
                    $sheet->getStyle ( 'M6:M' . $totalRow )->getNumberFormat ()->setFormatCode ( '#,##0' );
                    $sheet->getStyle ( 'N6:N' . $totalRow )->getNumberFormat ()->setFormatCode ( '#,##0' );
                    $sheet->getStyle ( 'O6:O' . $totalRow )->getNumberFormat ()->setFormatCode ( '#,##0' );
                    $sheet->getStyle ( 'P6:P' . $totalRow )->getNumberFormat ()->setFormatCode ( '#,##0' );
                    $sheet->getStyle ( 'Q6:Q' . $totalRow )->getNumberFormat ()->setFormatCode ( '#,##0' );
                    $sheet->getStyle ( 'R6:R' . $totalRow )->getNumberFormat ()->setFormatCode ( '#,##0' );
                    $sheet->getStyle ( 'S6:S' . $totalRow )->getNumberFormat ()->setFormatCode ( '#,##0' );
                    $sheet->getStyle ( 'T6:T' . $totalRow )->getNumberFormat ()->setFormatCode ( '#,##0' );
                    $sheet->getStyle ( 'U6:U' . $totalRow )->getNumberFormat ()->setFormatCode ( '#,##0' );
                    $sheet->getStyle ( 'V6:V' . $totalRow )->getNumberFormat ()->setFormatCode ( '#,##0' );
                    $sheet->getStyle ( 'W6:W' . $totalRow )->getNumberFormat ()->setFormatCode ( '#,##0' );
                    $sheet->getStyle ( 'X6:X' . $totalRow )->getNumberFormat ()->setFormatCode ( '#,##0' );
                    $sheet->getStyle ( 'Y6:Y' . $totalRow )->getNumberFormat ()->setFormatCode ( '#,##0' );
                    $sheet->getStyle ( 'Z6:Z' . $totalRow )->getNumberFormat ()->setFormatCode ( '#,##0' );
                    $sheet->getStyle ( 'AA6:AA' . $totalRow )->getNumberFormat ()->setFormatCode ( '#,##0' );
                    $sheet->getStyle ( 'AB6:AB' . $totalRow )->getNumberFormat ()->setFormatCode ( '#,##0' );
                    $sheet->getStyle ( 'AC6:AC' . $totalRow )->getNumberFormat ()->setFormatCode ( '#,##0' );
                    $sheet->getStyle ( 'AD6:AD' . $totalRow )->getNumberFormat ()->setFormatCode ( '#,##0' );
                    $sheet->getStyle ( 'AE6:AE' . $totalRow )->getNumberFormat ()->setFormatCode ( '#,##0' );
                    $sheet->getStyle ( 'AF6:AF' . $totalRow )->getNumberFormat ()->setFormatCode ( '#,##0' );
                    $sheet->getStyle ( 'AG6:AG' . $totalRow )->getNumberFormat ()->setFormatCode ( '#,##0' );
                    $sheet->getStyle ( 'AH6:AH' . $totalRow )->getNumberFormat ()->setFormatCode ( '#,##0' );
                    $sheet->getStyle ( 'AI6:AI' . $totalRow )->getNumberFormat ()->setFormatCode ( '#,##0' );
                    $sheet->getStyle ( 'AJ6:AJ' . $totalRow )->getNumberFormat ()->setFormatCode ( '#,##0' );
                    $sheet->getStyle ( 'AK6:AK' . $totalRow )->getNumberFormat ()->setFormatCode ( '#,##0' );
                    $sheet->getStyle ( 'AL6:AL' . $totalRow )->getNumberFormat ()->setFormatCode ( '#,##0' );
                    $sheet->getStyle ( 'AM6:AM' . $totalRow )->getNumberFormat ()->setFormatCode ( '#,##0' );
                    $sheet->getStyle ( 'AN6:AN' . $totalRow )->getNumberFormat ()->setFormatCode ( '#,##0' );
                    $sheet->getStyle ( 'AO6:AO' . $totalRow )->getNumberFormat ()->setFormatCode ( '#,##0' );
                    $sheet->getStyle ( 'AP6:AP' . $totalRow )->getNumberFormat ()->setFormatCode ( '#,##0' );
                }
                
                // Jika tipe ATB adalah 'Mutasi Proyek', bekukan kolom A sampai L
                if ($this->tipe === "Mutasi Proyek") {
                    $sheet->freezePane('M5'); // Membekukan kolom A sampai L (kolom L tidak bergerak)
                } else {
                    // Jika bukan 'Mutasi Proyek', kamu bisa menyesuaikan freeze sesuai kebutuhan lain
                    $sheet->freezePane('L5'); // Default freeze dari A sampai K
                }
            },
        ];
    }
}
?>