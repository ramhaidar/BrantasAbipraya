<?php

namespace App\Imports;

use App\Models\ATB;
use App\Models\Proyek;
use App\Models\Komponen;
use App\Models\FirstGroup;
use App\Models\SecondGroup;
use App\Models\Saldo;
use App\Models\MasterData;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Auth;

class ATBImport implements ToCollection, WithHeadingRow
{
    // Mapping kode untuk Perbaikan
    protected $kodePerbaikan = [
        'A1' => 'Cabin',
        'A2' => 'Engine System',
        'A3' => 'Transmission System',
        'A4' => 'Chassis & Swing Machinery',
        'A5' => 'Differential System',
        'A6' => 'Electrical System',
        'A7' => 'Hydraulic / Pneumatic System',
        'A8' => 'Steering System',
        'A9' => 'Brake System',
        'A10' => 'Suspension',
        'A11' => 'Attachment',
        'A12' => 'Undercarriage',
        'A13' => 'Final Drive',
        'A14' => 'Freight Cost',
    ];

    // Mapping kode untuk Maintenance Kit dalam Pemeliharaan
    protected $kodeMaintenanceKit = [
        'B11' => 'Oil Filter',
        'B12' => 'Fuel Filter',
        'B13' => 'Air Filter',
        'B14' => 'Hydraulic Filter',
        'B15' => 'Transmission Filter',
        'B16' => 'Differential Filter',
    ];

    // Mapping kode untuk Oil Lubricants dalam Pemeliharaan
    protected $kodeOilLubricants = [
        'B21' => 'Engine Oil',
        'B22' => 'Hydraulic Oil',
        'B23' => 'Transmission Oil',
        'B24' => 'Final Drive Oil',
        'B25' => 'Swing & Damper Oil',
        'B26' => 'Differential Oil',
        'B27' => 'Grease',
        'B28' => 'Brake & Power Steering Fluid',
        'B29' => 'Coolant',
    ];

    // Mapping kode untuk Tyre dalam Pemeliharaan
    protected $kodeTyre = [
        'B3' => 'Tyre',
    ];

    // Mapping kode untuk Workshop
    protected $kodeWorkshop = [
        'C1' => 'Workshop',
    ];

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) 
        {
            // Mendapatkan atau membuat Proyek berdasarkan nama proyek
            $proyek = Proyek::firstOrCreate(['nama_proyek' => $row['proyek']]);

            // Menentukan FirstGroup dan SecondGroup berdasarkan kode komponen
            if (isset($this->kodePerbaikan[$row['komponen']])) {
                $firstGroup = FirstGroup::create(['name' => 'PERBAIKAN']);
                $secondGroup = null; // Tidak ada second group untuk perbaikan
            } elseif (isset($this->kodeMaintenanceKit[$row['komponen']])) {
                $firstGroup = FirstGroup::create(['name' => 'PEMELIHARAAN']);
                $secondGroup = SecondGroup::create(['name' => 'MAINTENANCE KIT']);
            } elseif (isset($this->kodeOilLubricants[$row['komponen']])) {
                $firstGroup = FirstGroup::create(['name' => 'PEMELIHARAAN']);
                $secondGroup = SecondGroup::create(['name' => 'OIL & LUBRICANTS']);
            } elseif (isset($this->kodeTyre[$row['komponen']])) {
                $firstGroup = FirstGroup::create(['name' => 'PEMELIHARAAN']);
                $secondGroup = SecondGroup::create(['name' => 'Tyre']);
            } elseif (isset($this->kodeWorkshop[$row['komponen']])) {
                $firstGroup = FirstGroup::create(['name' => 'WAREHOUSE']);
                $secondGroup = null;
            } else {
                continue; // Skip jika kode komponen tidak ditemukan
            }

            // Memastikan second_group_id terisi jika second group ada
            $secondGroupId = $secondGroup ? $secondGroup->id : null;

            // Cek atau buat entri di tabel komponen berdasarkan kombinasi kode, first_group_id, second_group_id
            $komponen = Komponen::create([
                'kode' => $row['komponen'],
                'first_group_id' => $firstGroup->id,
                'second_group_id' => $secondGroupId,
            ]);

            // Membuat atau memperbarui entry di tabel Saldo
            $saldo = Saldo::create([
                'current_quantity' => $row['quantity'],
                'net' => $row['net'],
            ]);

            // Ambil ID user yang sedang login
            $userId = Auth::id();

            // Cek atau buat entri di tabel MasterData berdasarkan kombinasi supplier, sparepart, dan part_number
            $masterData = MasterData::firstOrCreate([
                'supplier'    => $row['supplier'],
                'sparepart'   => $row['sparepart'],
                'part_number' => $row['part_number'],
            ], [
                'id_user' => $userId, // Menyimpan ID user yang sedang login
                'buffer_stock' => null, // Atur buffer_stock sebagai null ketika entri baru dibuat
            ]);

            // Persiapan data untuk input ke tabel ATB
            $atbData = [
                'tipe'           => $row['tipe'],
                'tanggal'        => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['tanggal']),
                'quantity'       => $row['quantity'],
                'satuan'         => $row['satuan'],
                'harga'          => $row['harga'],
                'net'            => $row['net'],
                'ppn'            => $row['ppn'],
                'bruto'          => $row['bruto'],
                'id_komponen'    => $komponen->id, // ID Komponen yang diambil dari langkah sebelumnya
                'id_saldo'       => $saldo->id,
                'id_proyek'      => $proyek->id,
                'id_master_data' => $masterData->id, // ID dari tabel MasterData
            ];

            // Tambahkan 'asal_proyek' jika tipe ATB adalah "Mutasi Proyek"
            if ($row['tipe'] === 'Mutasi Proyek') {
                $atbData['asal_proyek'] = $row['asal_proyek'] ?? null;
            }

            // Buat entri ATB baru
            ATB::create($atbData);
        }
    }
}
