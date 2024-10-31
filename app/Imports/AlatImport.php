<?php

namespace App\Imports;

use App\Models\Alat;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class AlatImport implements ToModel, WithHeadingRow, WithMultipleSheets
{
    protected $id_user;

    // Constructor untuk menerima id_user
    public function __construct ( $id_user )
    {
        $this->id_user = $id_user;
    }

    /**
     * Pilih sheet yang akan diimport
     */
    public function sheets () : array
    {
        return [ 
            'DATABASE ALAT' => $this, // Sheet yang kita targetkan adalah "DATABASE_ALAT"
        ];
    }

    /**
     * Mapping kolom dari Excel ke kolom di model Alat
     */
    public function model ( array $row )
    {
        return new Alat( [ 
            'kode_alat'  => $row[ 'kode_alat' ] ?? '-',    // Ganti null dengan "-"
            'jenis_alat' => $row[ 'jenis_alat' ] ?? '-',   // Ganti null dengan "-"
            'merek_alat' => $row[ 'merk' ] ?? '-',         // Ganti null dengan "-"
            'tipe_alat'  => $row[ 'type' ] ?? '-',         // Ganti null dengan "-"
            'id_user'    => $this->id_user,              // Ambil id_user dari konstruktor
        ] );
    }
}
