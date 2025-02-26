<?php

namespace Database\Seeders;

use App\Models\MasterDataAlat;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\MasterDataAlatImport;

class MasterDataAlatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run ()
    {
        // Definisikan path ke file Excel
        $filePath = storage_path ( 'app/public/seeders/DataAlat.xlsx' );

        // Load data dari sheet Excel yang ditentukan
        $data = Excel::toArray ( new MasterDataAlatImport, $filePath );

        // Tentukan sheet yang ingin diproses (sheet dengan nama "DATABASE ALAT")
        $sheet = $data[ 0 ]; // Misalkan data ada pada sheet pertama

        // Iterasi melalui setiap baris, mulai dari baris ke-2 (index 1) untuk menghindari header
        foreach ( $sheet as $index => $row )
        {
            // Lewati baris pertama yang merupakan header (index 0)
            if ( $index == 0 ) continue;

            // Pastikan kolom 0 (kode_alat) dan kolom 2 (jenis_alat) tidak kosong
            if ( ! isset ( $row[ 0 ] ) || empty ( $row[ 0 ] ) || ! isset ( $row[ 2 ] ) || empty ( $row[ 2 ] ) )
            {
                // Jika kolom 0 atau kolom 2 kosong, skip baris ini
                continue;
            }

            // Ganti nilai kosong dengan "-" untuk kolom selain kolom 0 dan kolom 2
            $kodeAlat     = $row[ 0 ]; // KODE ALAT [V] (Column A -> index 0)
            $jenisAlat    = $row[ 2 ]; // JENIS ALAT [V] (Column C -> index 2)
            $merekAlat    = isset ( $row[ 5 ] ) && ! empty ( $row[ 5 ] ) ? $row[ 5 ] : "-"; // MERK [V] (Column F -> index 5)
            $tipeAlat     = isset ( $row[ 6 ] ) && ! empty ( $row[ 6 ] ) ? $row[ 6 ] : "-"; // TYPE [V] (Column G -> index 6)
            $serialNumber = isset ( $row[ 8 ] ) && ! empty ( $row[ 8 ] ) ? $row[ 8 ] : "-"; // SN [V] (Column I -> index 8)

            // Use firstOrCreate instead of create to prevent duplicate entries
            MasterDataAlat::firstOrCreate (
                [ 'kode_alat' => $kodeAlat ], // Search by the kode_alat as unique identifier
                [ 
                    'jenis_alat'    => $jenisAlat,
                    'merek_alat'    => $merekAlat,
                    'tipe_alat'     => $tipeAlat,
                    'serial_number' => $serialNumber,
                ]
            );
        }
    }
}
