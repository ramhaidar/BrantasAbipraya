<?php
namespace Database\Seeders;

use App\Models\Proyek;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ProyekSeeder extends Seeder
{
    public function run () : void
    {
        $data = [ 
            [ 'nama_proyek' => "PROYEK PEMBANGUNAN BENDUNGAN KEUREUTO ACEH" ],
            [ 'nama_proyek' => "PROYEK IRIGASI D.I. LEMATANG KOTA PAGAR ALAM PHASE II PAKET 1" ],
            [ 'nama_proyek' => "PROYEK PEKERJAAN RANCANG BANGUN RUMAH SUSUN STASIUN TANJUNG BARAT JAKARTA TIMUR" ],
            [ 'nama_proyek' => "PROYEK HUNIAN TETAP PASCA BENCANA GEMPA CIANJUR TAHAP 3" ],
            [ 'nama_proyek' => "PROYEK PEMBANGUNAN BENDUNGAN BENER PAKET 1 (MYC)" ],
            [ 'nama_proyek' => "PROYEK PEMBANGUNAN BENDUNGAN BENER PAKET 4 (MYC)" ],
            [ 'nama_proyek' => "PT PARAHITA DANAPATI (BENER PAKET 1)" ],
            [ 'nama_proyek' => "PT RINDU IDOLA SEMESTA (BENER PAKET 4)" ],
            [ 'nama_proyek' => "PROYEK PEMBANGUNAN BENDUNGAN JRAGUNG PAKET III" ],
            [ 'nama_proyek' => "PROYEK PEMBANGUNAN SARANA PENGENDALIAN BANJIR SUNGAI BOGOWONTO" ],
            [ 'nama_proyek' => "PROYEK PEMBANGUNAN JALAN TOL PROBOLINGGO - BANYUWANGI PAKET 1" ],
            [ 'nama_proyek' => "PROYEK PEMBANGUNAN BENDUNGAN SIDAN (LANJUTAN)" ],
            [ 'nama_proyek' => "PT ANANDA PRATAMA (BENDUNGAN SIDAN LANJUTAN)" ],
            [ 'nama_proyek' => "PROYEK JARINGAN IRIGASI D.I. BINTANG BANO" ],
            [ 'nama_proyek' => "PT KARSA PILAR KONSTRUKSI (PERUMAHAN KUPANG)" ],
            [ 'nama_proyek' => "PROYEK PEMBANGUNAN BENDUNGAN MBAY" ],
            [ 'nama_proyek' => "PT BUMI SEKAR INDAH (BENDUNGAN MBAY)" ],
            [ 'nama_proyek' => "PT GRI (GENANGAN CIPANAS)" ],
            [ 'nama_proyek' => "PROYEK PEMBANGUNAN RUSUN ASN 4 - IKN" ],
            [ 'nama_proyek' => "PROYEK EMBUNG KIPP - IKN" ],
            [ 'nama_proyek' => "PROYEK PEMBANGUNAN BENDUNGAN SEPAKU SEMOI" ],
            [ 'nama_proyek' => "PROYEK PEMBANGUNAN BENDUNGAN SEPAKU SEMOI (LANJUTAN)" ],
            [ 'nama_proyek' => "PROYEK PENANGANAN BANJIR SUNGAI SEPAKU - IKN" ],
            [ 'nama_proyek' => "PROYEK SPAM PAKET 1 - IKN" ],
            [ 'nama_proyek' => "PROYEK SPAM PAKET 2 - IKN" ],
            [ 'nama_proyek' => "PROYEK PENATAAN SUMBU KEBANGSAAN TAHAP 1 - IKN" ],
            [ 'nama_proyek' => "PROYEK PENATAAN SUMBU KEBANGSAAN TAHAP 2 - IKN" ],
            [ 'nama_proyek' => "PROYEK JALAN BEBAS HAMBATAN SEKSI 6B - IKN" ],
            [ 'nama_proyek' => "PROYEK PEMBANGUNAN JALAN TOL IKN SEGMEN KARANGJOANG - KKT KARIANGAU - IKN" ],
            [ 'nama_proyek' => "PROYEK JALAN KERJA KAWASAN SUB BWP 1B 1C - IKN" ],
            [ 'nama_proyek' => "PROYEK PEMBANGUNAN TEMPAT PENGOLAHAN SAMPAH TERPADU - IKN" ],
            [ 'nama_proyek' => "PROYEK PEMBANGUNAN JEMBATAN NANGA PINOH - ELLA HILIR - BTS KALTENG" ],
            [ 'nama_proyek' => "PROYEK BENDUNGAN BUDONG - BUDONG" ],
            [ 'nama_proyek' => "PROYEK PEMBANGUNAN BENDUNGAN BULANGO ULU" ],
            [ 'nama_proyek' => "PT SAMA ROKHMA INDONESIA (BULANGO ULU)" ],
            [ 'nama_proyek' => "PROYEK JARINGAN IRIGASI D.I. BALIASE" ],
            [ 'nama_proyek' => "PROYEK REHABILITASI DAN REKONSTRUKSI GEDUNG DPRD MAMUJU" ],
            [ 'nama_proyek' => "PROYEK RUMAH SAKIT UPT VERTIKAL PAPUA" ],
            [ 'nama_proyek' => "PROYEK ELEVATED KARAWANG" ],
            [ 'nama_proyek' => "POOL ALAT SUBANG" ],
            [ 'nama_proyek' => "PABRIK SUBANG" ],
            [ 'nama_proyek' => "OSP IKN" ],
            [ 'nama_proyek' => "PT BUMI GRESIK (BENDUNGAN BUDONG - BUDONG)" ],
            [ 'nama_proyek' => "PT BUMI GRESIK (BENDUNGAN MBAY)" ],
            [ 'nama_proyek' => "PT ANANDA PRATAMA (BENDUNGAN JRAGUNG)" ],
            [ 'nama_proyek' => "INSTALASI PENYEDIA AIR BERSIH (IPA - IKN)" ],
            [ 'nama_proyek' => "WING 2 PUPR IKN (GEDUNG PUPR 5 TOWER)" ],
            [ 'nama_proyek' => "RS ABDI WALUYO IKN" ],
            [ 'nama_proyek' => "BENDUNGAN MARANGKAYU" ],
            [ 'nama_proyek' => "RS DJUANSIH MAJALENGKA" ],
            [ 'nama_proyek' => "PT GODAM SOLUSI (BENDUNGAN SIDAN LANJUTAN)" ],
            [ 'nama_proyek' => "PT JATI KENCANA (BENDUNGAN JRAGUNG)" ],
            [ 'nama_proyek' => "PROYEK EMBUNG LANJUTAN KIPP - IKN" ],
            [ 'nama_proyek' => "PROYEK JALAN LINGKAR SEPAKU" ],
            [ 'nama_proyek' => "PROYEK PERUMAHAN KUPANG" ],
            [ 'nama_proyek' => "PT DINAR MULIA BARAKAH (BENER PAKET 1)" ],
            [ 'nama_proyek' => "PT TRANSSINDO HUTAMA KARYA (BENER PAKET 4)" ],
            [ 'nama_proyek' => "CV NURA SAKTI (BENER 4)" ],
            [ 'nama_proyek' => "PT BUMI KARSA (PROYEK BULANGO ULU PAKET 3)" ],
            [ 'nama_proyek' => "MODULAR TNI IKN" ],
            [ 'nama_proyek' => "PT BUMI KARSA (PROYEK PRESERVASI RUAS JALAN BILUHU BARAT)" ],

        ];
        foreach ( $data as $item )
        {
            Proyek::create ( $item );
        }
    }
}

