<?php

namespace App\Exports;

use App\Models\Saldo;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Carbon\Carbon;

class SaldoExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithCustomStartCell, WithEvents
{
    protected $proyek;
    protected $tipe;

    public function __construct($tipe, $proyek)
    {
        $this->proyek = $proyek;
        $this->tipe = $tipe;
    }

    public function collection()
    {
        // Ambil data saldo berdasarkan proyek dan tipe
        return Saldo::with(['komponen', 'masterData'])
            ->whereHas('atb', function ($query) {
                $query->where('id_proyek', $this->proyek)
                    ->where('tipe', $this->tipe);
            })
            ->get();
    }

    public function startCell(): string
    {
        return 'A2';
    }

    public function headings(): array
    {
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
    public function styles(Worksheet $sheet)
    {
        $highestRow = $sheet->getHighestRow();

        // Merging cells for the headers
        $sheet->mergeCells('A2:A4'); // Merge for TANGGAL
        $sheet->mergeCells('B2:B4'); // Merge for KODE
        $sheet->mergeCells('C2:C4'); // Merge for SUPPLIER
        $sheet->mergeCells('D2:D4'); // Merge for SPAREPART
        $sheet->mergeCells('E2:E4'); // Merge for PART NUMBER
        $sheet->mergeCells('F2:F4'); // Merge for QTY
        $sheet->mergeCells('G2:G4'); // Merge for SAT
        $sheet->mergeCells('H2:K3'); // Merge for NILAI section (HARGA, NET, PPN, BRUTO)
        $sheet->mergeCells('L2:Y3'); // Merge for PERBAIKAN section
        $sheet->mergeCells('Z2:AO2'); // Merge for PEMELIHARAAN
        $sheet->mergeCells('Z3:AE3'); // Merge for MAINTENANCE KIT
        $sheet->mergeCells('AF3:AN3'); // Merge for OIL & LUBRICANTS
        $sheet->mergeCells('AO3:AO4'); // Merge for TYRE
        $sheet->mergeCells('AP2:AP4'); // Merge for WORKSHOP

        // Styling headers untuk tipe lainnya
        $sheet->getStyle('A2:AP5')->getFont()->setBold(true);
        $sheet->getStyle('A2:AP5')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A2:AP5')->getAlignment()->setVertical('center');
        $sheet->getStyle('A2:AP5')->getAlignment()->setWrapText(true);

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
        
        $sheet->getStyle('A2:AP4')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        $sheet->getStyle('A2:AP' . $highestRow)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Styling untuk tipe lain (background color mulai dari kolom L)
        $sheet->getStyle('L2:AP' . $highestRow)->applyFromArray([
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color'    => ['rgb' => 'C0C0C0'] // Warna background tipe non-Mutasi Proyek
            ]
        ]);

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

        return [
            2 => ['font' => ['bold' => true]],
            3 => ['font' => ['bold' => true]],
        ];
    }


    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                // Freeze the first row and column
                $event->sheet->freezePane('A3');
            },
        ];
    }
}
