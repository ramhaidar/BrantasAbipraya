<?php

namespace Database\Seeders;

use App\Models\Alat;
use App\Models\User;
use App\Imports\AlatImport;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;

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

    public function run () : void
    {
        // Inisialisasi Faker
        $faker = Faker::create ();

        // Cari user pertama dengan role 'Pegawai'
        $user = User::where ( 'role', 'Pegawai' )->first ();

        // Jika tidak ada user dengan role 'Pegawai', hentikan proses seeding
        if ( ! $user )
        {
            $this->command->error ( 'Tidak ditemukan pengguna dengan role Pegawai.' );
            return;
        }

        // Ambil id_user dari user yang ditemukan
        $id_user = $user->id;

        $data = [ 
            [ 'jenis_alat' => 'ASPHALT DISTRIBUTOR DAN TRACKTOR HEAD', 'tipe_alat' => 'FUSOFM517HL/DS-60DD', 'kode_alat' => 'AD 001-6000', 'merek_alat' => 'Caterpillar', 'id_user' => $id_user ],
            [ 'jenis_alat' => 'ASPHALT FINISHER', 'tipe_alat' => 'AP300', 'kode_alat' => 'AF 001-4', 'merek_alat' => 'Komatsu', 'id_user' => $id_user ],
            [ 'jenis_alat' => 'ASPHALT FINISHER', 'tipe_alat' => 'SD2500CS', 'kode_alat' => 'AF 001-6,6', 'merek_alat' => 'Hitachi', 'id_user' => $id_user ],
            [ 'jenis_alat' => 'ASPHALT FINISHER', 'tipe_alat' => 'SD2500CS', 'kode_alat' => 'AF 002-6,6', 'merek_alat' => 'Volvo', 'id_user' => $id_user ],
            [ 'jenis_alat' => 'AGREGAT PLANT', 'tipe_alat' => 'FM 260 JM', 'kode_alat' => 'AGP 001-40', 'merek_alat' => 'Liebherr', 'id_user' => $id_user ],
            [ 'jenis_alat' => 'ASPHALT MIXING PLANT', 'tipe_alat' => 'BAMP 1000-FA', 'kode_alat' => 'AMP 001-80', 'merek_alat' => 'Hyundai', 'id_user' => $id_user ],
            [ 'jenis_alat' => 'ASPHALT MIXING PLANT', 'tipe_alat' => 'BAMP-800P-SA', 'kode_alat' => 'AMP 002-50', 'merek_alat' => 'Doosan', 'id_user' => $id_user ],
            [ 'jenis_alat' => 'ASPHALT MIXING PLANT', 'tipe_alat' => 'AZP 800', 'kode_alat' => 'AMP 003-50', 'merek_alat' => 'JCB', 'id_user' => $id_user ],
            [ 'jenis_alat' => 'ASPHALT SPRAYER', 'tipe_alat' => 'R 180', 'kode_alat' => 'AS 001-1000', 'merek_alat' => 'Caterpillar', 'id_user' => $id_user ],
            [ 'jenis_alat' => 'ASPHALT SPRAYER', 'tipe_alat' => 'TF 55-RD', 'kode_alat' => 'AS 002-1000', 'merek_alat' => 'Komatsu', 'id_user' => $id_user ],
            [ 'jenis_alat' => 'ASPHALT SPRAYER', 'tipe_alat' => 'TF 55-RD', 'kode_alat' => 'AS 003-1000', 'merek_alat' => 'Hitachi', 'id_user' => $id_user ],
            [ 'jenis_alat' => 'ASPHALT SPRAYER', 'tipe_alat' => 'RUTRA R1000', 'kode_alat' => 'AS 004-1000', 'merek_alat' => 'Volvo', 'id_user' => $id_user ],
            [ 'jenis_alat' => 'AGITATOR TRUCK', 'tipe_alat' => 'HD 130 PS', 'kode_alat' => 'AT 007-3', 'merek_alat' => 'Liebherr', 'id_user' => $id_user ],
            [ 'jenis_alat' => 'AGITATOR TRUCK', 'tipe_alat' => 'HD 130 PS', 'kode_alat' => 'AT 008-3', 'merek_alat' => 'Hyundai', 'id_user' => $id_user ],
            [ 'jenis_alat' => 'AGITATOR TRUCK', 'tipe_alat' => 'FUSO FV416J', 'kode_alat' => 'AT 009-5', 'merek_alat' => 'Doosan', 'id_user' => $id_user ],
            [ 'jenis_alat' => 'AGITATOR TRUCK', 'tipe_alat' => 'FUSO FV416J', 'kode_alat' => 'AT 010-5', 'merek_alat' => 'JCB', 'id_user' => $id_user ],
            [ 'jenis_alat' => 'AGITATOR TRUCK', 'tipe_alat' => 'FM 260 JM', 'kode_alat' => 'AT 011-7', 'merek_alat' => 'Caterpillar', 'id_user' => $id_user ],
            [ 'jenis_alat' => 'AGITATOR TRUCK', 'tipe_alat' => 'FVZ 34K MX 6X4', 'kode_alat' => 'AT 012-7', 'merek_alat' => 'Komatsu', 'id_user' => $id_user ],
            [ 'jenis_alat' => 'AGITATOR TRUCK', 'tipe_alat' => 'FVZ 34K MX 6X4', 'kode_alat' => 'AT 013-7', 'merek_alat' => 'Hitachi', 'id_user' => $id_user ],
            [ 'jenis_alat' => 'AGITATOR TRUCK', 'tipe_alat' => 'FM260JM', 'kode_alat' => 'AT 014-7', 'merek_alat' => 'Volvo', 'id_user' => $id_user ],
            [ 'jenis_alat' => 'AGITATOR TRUCK', 'tipe_alat' => 'FM 260 JM', 'kode_alat' => 'AT 015-7', 'merek_alat' => 'Liebherr', 'id_user' => $id_user ],
            [ 'jenis_alat' => 'AGITATOR TRUCK', 'tipe_alat' => 'FM 260 JN', 'kode_alat' => 'AT 016-7', 'merek_alat' => 'Hyundai', 'id_user' => $id_user ],
            [ 'jenis_alat' => 'AGITATOR TRUCK', 'tipe_alat' => 'FM260JM', 'kode_alat' => 'AT 017-7', 'merek_alat' => 'Doosan', 'id_user' => $id_user ],
            [ 'jenis_alat' => 'AGITATOR TRUCK', 'tipe_alat' => 'FVZ 34K MX', 'kode_alat' => 'AT 018-7', 'merek_alat' => 'JCB', 'id_user' => $id_user ],
            [ 'jenis_alat' => 'AGITATOR TRUCK', 'tipe_alat' => 'FVZ 34K MX', 'kode_alat' => 'AT 019-7', 'merek_alat' => 'Caterpillar', 'id_user' => $id_user ],
        ];

        foreach ( $data as &$item )
        {
            // Tambahkan kolom 'nama_proyek' dengan Faker
            $item[ 'nama_proyek' ] = $faker->company;
        }

        foreach ( $data as $item )
        {
            Alat::create ( $item );
        }

        // Path ke file Excel yang ingin di-import
        Excel::import ( new AlatImport( $id_user ), storage_path ( 'app/public/ALAT.xlsx' ) );
    }
}
