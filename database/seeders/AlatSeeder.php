<?php

namespace Database\Seeders;

use App\Models\Alat;
use App\Models\User;
use App\Imports\AlatImport;
use Faker\Factory as Faker;
use App\Models\MasterDataAlat;
use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\MasterDataAlatImport;

class AlatSeeder extends Seeder
{
    // public function run ()
    // {
    //     // Inisialisasi Faker
    //     $faker = Faker::create ();

    //     // Cari user pertama dengan role 'Pegawai'
    //     $user = User::where ( 'role', 'Pegawai' )->first ();

    //     // Jika tidak ada user dengan role 'Pegawai', hentikan proses seeding
    //     if ( ! $user )
    //     {
    //         $this->command->error ( 'Tidak ditemukan pengguna dengan role Pegawai.' );
    //         return;
    //     }

    //     // Menggunakan id_user dari user pertama dengan role Pegawai
    //     $id_user = $user->id;

    //     $data = [ 
    //         [ 'jenis_alat' => 'ASPHALT DISTRIBUTOR DAN TRACKTOR HEAD', 'tipe_alat' => 'FUSOFM517HL/DS-60DD', 'kode_alat' => 'AD 001-6000', 'merek_alat' => 'Caterpillar', 'id_user' => $id_user ],
    //         [ 'jenis_alat' => 'ASPHALT FINISHER', 'tipe_alat' => 'AP300', 'kode_alat' => 'AF 001-4', 'merek_alat' => 'Komatsu', 'id_user' => $id_user ],
    //         [ 'jenis_alat' => 'ASPHALT FINISHER', 'tipe_alat' => 'SD2500CS', 'kode_alat' => 'AF 001-6,6', 'merek_alat' => 'Hitachi', 'id_user' => $id_user ],
    //         [ 'jenis_alat' => 'ASPHALT FINISHER', 'tipe_alat' => 'SD2500CS', 'kode_alat' => 'AF 002-6,6', 'merek_alat' => 'Volvo', 'id_user' => $id_user ],
    //         [ 'jenis_alat' => 'AGREGAT PLANT', 'tipe_alat' => 'FM 260 JM', 'kode_alat' => 'AGP 001-40', 'merek_alat' => 'Liebherr', 'id_user' => $id_user ],
    //         [ 'jenis_alat' => 'ASPHALT MIXING PLANT', 'tipe_alat' => 'BAMP 1000-FA', 'kode_alat' => 'AMP 001-80', 'merek_alat' => 'Hyundai', 'id_user' => $id_user ],
    //         [ 'jenis_alat' => 'ASPHALT MIXING PLANT', 'tipe_alat' => 'BAMP-800P-SA', 'kode_alat' => 'AMP 002-50', 'merek_alat' => 'Doosan', 'id_user' => $id_user ],
    //         [ 'jenis_alat' => 'ASPHALT MIXING PLANT', 'tipe_alat' => 'AZP 800', 'kode_alat' => 'AMP 003-50', 'merek_alat' => 'JCB', 'id_user' => $id_user ],
    //         [ 'jenis_alat' => 'ASPHALT SPRAYER', 'tipe_alat' => 'R 180', 'kode_alat' => 'AS 001-1000', 'merek_alat' => 'Caterpillar', 'id_user' => $id_user ],
    //         [ 'jenis_alat' => 'ASPHALT SPRAYER', 'tipe_alat' => 'TF 55-RD', 'kode_alat' => 'AS 002-1000', 'merek_alat' => 'Komatsu', 'id_user' => $id_user ],
    //         [ 'jenis_alat' => 'ASPHALT SPRAYER', 'tipe_alat' => 'TF 55-RD', 'kode_alat' => 'AS 003-1000', 'merek_alat' => 'Hitachi', 'id_user' => $id_user ],
    //         [ 'jenis_alat' => 'ASPHALT SPRAYER', 'tipe_alat' => 'RUTRA R1000', 'kode_alat' => 'AS 004-1000', 'merek_alat' => 'Volvo', 'id_user' => $id_user ],
    //         [ 'jenis_alat' => 'AGITATOR TRUCK', 'tipe_alat' => 'HD 130 PS', 'kode_alat' => 'AT 007-3', 'merek_alat' => 'Liebherr', 'id_user' => $id_user ],
    //         [ 'jenis_alat' => 'AGITATOR TRUCK', 'tipe_alat' => 'HD 130 PS', 'kode_alat' => 'AT 008-3', 'merek_alat' => 'Hyundai', 'id_user' => $id_user ],
    //         [ 'jenis_alat' => 'AGITATOR TRUCK', 'tipe_alat' => 'FUSO FV416J', 'kode_alat' => 'AT 009-5', 'merek_alat' => 'Doosan', 'id_user' => $id_user ],
    //         [ 'jenis_alat' => 'AGITATOR TRUCK', 'tipe_alat' => 'FUSO FV416J', 'kode_alat' => 'AT 010-5', 'merek_alat' => 'JCB', 'id_user' => $id_user ],
    //         [ 'jenis_alat' => 'AGITATOR TRUCK', 'tipe_alat' => 'FM 260 JM', 'kode_alat' => 'AT 011-7', 'merek_alat' => 'Caterpillar', 'id_user' => $id_user ],
    //         [ 'jenis_alat' => 'AGITATOR TRUCK', 'tipe_alat' => 'FVZ 34K MX 6X4', 'kode_alat' => 'AT 012-7', 'merek_alat' => 'Komatsu', 'id_user' => $id_user ],
    //         [ 'jenis_alat' => 'AGITATOR TRUCK', 'tipe_alat' => 'FVZ 34K MX 6X4', 'kode_alat' => 'AT 013-7', 'merek_alat' => 'Hitachi', 'id_user' => $id_user ],
    //         [ 'jenis_alat' => 'AGITATOR TRUCK', 'tipe_alat' => 'FM260JM', 'kode_alat' => 'AT 014-7', 'merek_alat' => 'Volvo', 'id_user' => $id_user ],
    //         [ 'jenis_alat' => 'AGITATOR TRUCK', 'tipe_alat' => 'FM 260 JM', 'kode_alat' => 'AT 015-7', 'merek_alat' => 'Liebherr', 'id_user' => $id_user ],
    //         [ 'jenis_alat' => 'AGITATOR TRUCK', 'tipe_alat' => 'FM 260 JN', 'kode_alat' => 'AT 016-7', 'merek_alat' => 'Hyundai', 'id_user' => $id_user ],
    //         [ 'jenis_alat' => 'AGITATOR TRUCK', 'tipe_alat' => 'FM260JM', 'kode_alat' => 'AT 017-7', 'merek_alat' => 'Doosan', 'id_user' => $id_user ],
    //         [ 'jenis_alat' => 'AGITATOR TRUCK', 'tipe_alat' => 'FVZ 34K MX', 'kode_alat' => 'AT 018-7', 'merek_alat' => 'JCB', 'id_user' => $id_user ],
    //         [ 'jenis_alat' => 'AGITATOR TRUCK', 'tipe_alat' => 'FVZ 34K MX', 'kode_alat' => 'AT 019-7', 'merek_alat' => 'Caterpillar', 'id_user' => $id_user ],
    //     ];

    //     foreach ( $data as &$item )
    //     {
    //         // Tambahkan kolom 'nama_proyek' dengan Faker
    //         $item[ 'nama_proyek' ] = $faker->company;
    //     }

    //     foreach ( $data as $item )
    //     {
    //         Alat::create ( $item );
    //     }
    // }

    // public function run () : void
    // {
    //     // Inisialisasi Faker
    //     $faker = Faker::create ();

    //     // Cari user pertama dengan role 'Pegawai'
    //     $user = User::where ( 'role', 'Pegawai' )->first ();

    //     // Jika tidak ada user dengan role 'Pegawai', hentikan proses seeding
    //     if ( ! $user )
    //     {
    //         $this->command->error ( 'Tidak ditemukan pengguna dengan role Pegawai.' );
    //         return;
    //     }

    //     // Ambil id_user dari user yang ditemukan
    //     $id_user = $user->id;

    //     $data = [ 
    //         [ 'jenis_alat' => 'ASPHALT DISTRIBUTOR DAN TRACKTOR HEAD', 'tipe_alat' => 'FUSOFM517HL/DS-60DD', 'kode_alat' => 'AD 001-6000', 'merek_alat' => 'Caterpillar', 'id_user' => $id_user ],
    //         [ 'jenis_alat' => 'ASPHALT FINISHER', 'tipe_alat' => 'AP300', 'kode_alat' => 'AF 001-4', 'merek_alat' => 'Komatsu', 'id_user' => $id_user ],
    //         [ 'jenis_alat' => 'ASPHALT FINISHER', 'tipe_alat' => 'SD2500CS', 'kode_alat' => 'AF 001-6,6', 'merek_alat' => 'Hitachi', 'id_user' => $id_user ],
    //         [ 'jenis_alat' => 'ASPHALT FINISHER', 'tipe_alat' => 'SD2500CS', 'kode_alat' => 'AF 002-6,6', 'merek_alat' => 'Volvo', 'id_user' => $id_user ],
    //         [ 'jenis_alat' => 'AGREGAT PLANT', 'tipe_alat' => 'FM 260 JM', 'kode_alat' => 'AGP 001-40', 'merek_alat' => 'Liebherr', 'id_user' => $id_user ],
    //         [ 'jenis_alat' => 'ASPHALT MIXING PLANT', 'tipe_alat' => 'BAMP 1000-FA', 'kode_alat' => 'AMP 001-80', 'merek_alat' => 'Hyundai', 'id_user' => $id_user ],
    //         [ 'jenis_alat' => 'ASPHALT MIXING PLANT', 'tipe_alat' => 'BAMP-800P-SA', 'kode_alat' => 'AMP 002-50', 'merek_alat' => 'Doosan', 'id_user' => $id_user ],
    //         [ 'jenis_alat' => 'ASPHALT MIXING PLANT', 'tipe_alat' => 'AZP 800', 'kode_alat' => 'AMP 003-50', 'merek_alat' => 'JCB', 'id_user' => $id_user ],
    //         [ 'jenis_alat' => 'ASPHALT SPRAYER', 'tipe_alat' => 'R 180', 'kode_alat' => 'AS 001-1000', 'merek_alat' => 'Caterpillar', 'id_user' => $id_user ],
    //         [ 'jenis_alat' => 'ASPHALT SPRAYER', 'tipe_alat' => 'TF 55-RD', 'kode_alat' => 'AS 002-1000', 'merek_alat' => 'Komatsu', 'id_user' => $id_user ],
    //         [ 'jenis_alat' => 'ASPHALT SPRAYER', 'tipe_alat' => 'TF 55-RD', 'kode_alat' => 'AS 003-1000', 'merek_alat' => 'Hitachi', 'id_user' => $id_user ],
    //         [ 'jenis_alat' => 'ASPHALT SPRAYER', 'tipe_alat' => 'RUTRA R1000', 'kode_alat' => 'AS 004-1000', 'merek_alat' => 'Volvo', 'id_user' => $id_user ],
    //         [ 'jenis_alat' => 'AGITATOR TRUCK', 'tipe_alat' => 'HD 130 PS', 'kode_alat' => 'AT 007-3', 'merek_alat' => 'Liebherr', 'id_user' => $id_user ],
    //         [ 'jenis_alat' => 'AGITATOR TRUCK', 'tipe_alat' => 'HD 130 PS', 'kode_alat' => 'AT 008-3', 'merek_alat' => 'Hyundai', 'id_user' => $id_user ],
    //         [ 'jenis_alat' => 'AGITATOR TRUCK', 'tipe_alat' => 'FUSO FV416J', 'kode_alat' => 'AT 009-5', 'merek_alat' => 'Doosan', 'id_user' => $id_user ],
    //         [ 'jenis_alat' => 'AGITATOR TRUCK', 'tipe_alat' => 'FUSO FV416J', 'kode_alat' => 'AT 010-5', 'merek_alat' => 'JCB', 'id_user' => $id_user ],
    //         [ 'jenis_alat' => 'AGITATOR TRUCK', 'tipe_alat' => 'FM 260 JM', 'kode_alat' => 'AT 011-7', 'merek_alat' => 'Caterpillar', 'id_user' => $id_user ],
    //         [ 'jenis_alat' => 'AGITATOR TRUCK', 'tipe_alat' => 'FVZ 34K MX 6X4', 'kode_alat' => 'AT 012-7', 'merek_alat' => 'Komatsu', 'id_user' => $id_user ],
    //         [ 'jenis_alat' => 'AGITATOR TRUCK', 'tipe_alat' => 'FVZ 34K MX 6X4', 'kode_alat' => 'AT 013-7', 'merek_alat' => 'Hitachi', 'id_user' => $id_user ],
    //         [ 'jenis_alat' => 'AGITATOR TRUCK', 'tipe_alat' => 'FM260JM', 'kode_alat' => 'AT 014-7', 'merek_alat' => 'Volvo', 'id_user' => $id_user ],
    //         [ 'jenis_alat' => 'AGITATOR TRUCK', 'tipe_alat' => 'FM 260 JM', 'kode_alat' => 'AT 015-7', 'merek_alat' => 'Liebherr', 'id_user' => $id_user ],
    //         [ 'jenis_alat' => 'AGITATOR TRUCK', 'tipe_alat' => 'FM 260 JN', 'kode_alat' => 'AT 016-7', 'merek_alat' => 'Hyundai', 'id_user' => $id_user ],
    //         [ 'jenis_alat' => 'AGITATOR TRUCK', 'tipe_alat' => 'FM260JM', 'kode_alat' => 'AT 017-7', 'merek_alat' => 'Doosan', 'id_user' => $id_user ],
    //         [ 'jenis_alat' => 'AGITATOR TRUCK', 'tipe_alat' => 'FVZ 34K MX', 'kode_alat' => 'AT 018-7', 'merek_alat' => 'JCB', 'id_user' => $id_user ],
    //         [ 'jenis_alat' => 'AGITATOR TRUCK', 'tipe_alat' => 'FVZ 34K MX', 'kode_alat' => 'AT 019-7', 'merek_alat' => 'Caterpillar', 'id_user' => $id_user ],
    //     ];

    //     foreach ( $data as &$item )
    //     {
    //         // Tambahkan kolom 'nama_proyek' dengan Faker
    //         $item[ 'nama' ] = $faker->company;
    //     }

    //     foreach ( $data as $item )
    //     {
    //         Alat::create ( $item );
    //     }

    //     // Path ke file Excel yang ingin di-import
    //     Excel::import ( new AlatImport( $id_user ), storage_path ( 'app/public/ALAT.xlsx' ) );
    // }

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

            // Pastikan kolom yang dibutuhkan tidak kosong
            if ( isset ( $row[ 2 ], $row[ 0 ], $row[ 5 ], $row[ 6 ], $row[ 8 ] ) )
            {
                // Cek apakah nilai kolom tidak kosong atau null
                $jenisAlat    = ! empty ( $row[ 2 ] ) ? $row[ 2 ] : null; // JENIS ALAT [V] (Column C -> index 2)
                $kodeAlat     = ! empty ( $row[ 0 ] ) ? $row[ 0 ] : null; // KODE ALAT [V] (Column A -> index 0)
                $merekAlat    = ! empty ( $row[ 5 ] ) ? $row[ 5 ] : null; // MERK [V] (Column F -> index 5)
                $tipeAlat     = ! empty ( $row[ 6 ] ) ? $row[ 6 ] : null; // TYPE [V] (Column G -> index 6)
                $serialNumber = ! empty ( $row[ 8 ] ) ? $row[ 8 ] : null; // SN [V] (Column I -> index 8)

                // Jika salah satu kolom penting kosong, skip baris ini
                if ( $jenisAlat && $kodeAlat && $merekAlat && $tipeAlat && $serialNumber )
                {
                    // Insert data ke dalam tabel master_data_alat
                    MasterDataAlat::create ( [ 
                        'jenis_alat'    => $jenisAlat,
                        'kode_alat'     => $kodeAlat,
                        'merek_alat'    => $merekAlat,
                        'tipe_alat'     => $tipeAlat,
                        'serial_number' => $serialNumber,
                    ] );
                }
            }
        }
    }
}
