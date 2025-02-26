<?php
namespace Database\Seeders;

use App\Models\Proyek;
use Illuminate\Database\Seeder;

class ProyekSeeder extends Seeder
{
    public function run () : void
    {
        $proyek =
            [ 
                "BENDUNGAN BUDONG - BUDONG",
                "BENDUNGAN MARANGKAYU",
                "CV NURA SAKTI (BENER 4)",
                "ELEVATED KARAWANG",
                "EMBUNG KIPP - IKN",
                "EMBUNG LANJUTAN KIPP - IKN",
                "HUNIAN TETAP PASCA BENCANA GEMPA CIANJUR TAHAP 3",
                "INSTALASI PENYEDIA AIR BERSIH (IPA - IKN)",
                "IRIGASI D.I. LEMATANG KOTA PAGAR ALAM PHASE II PAKET 1",
                "JALAN BEBAS HAMBATAN SEKSI 6B - IKN",
                "JALAN KERJA KAWASAN SUB BWP 1B 1C - IKN",
                "JALAN LINGKAR SEPAKU",
                "JARINGAN IRIGASI D.I. BALIASE",
                "JARINGAN IRIGASI D.I. BINTANG BANO",
                "MODULAR TNI IKN",
                "OSP IKN",
                "PABRIK SUBANG",
                "PEKERJAAN RANCANG BANGUN RUMAH SUSUN STASIUN TANJUNG BARAT JAKARTA TIMUR",
                "PEMBANGUNAN BENDUNGAN BENER PAKET 1 (MYC)",
                "PEMBANGUNAN BENDUNGAN BENER PAKET 4 (MYC)",
                "PEMBANGUNAN BENDUNGAN BULANGO ULU",
                "PEMBANGUNAN BENDUNGAN JRAGUNG PAKET III",
                "PEMBANGUNAN BENDUNGAN KEUREUTO ACEH",
                "PEMBANGUNAN BENDUNGAN MBAY",
                "PEMBANGUNAN BENDUNGAN SEPAKU SEMOI (LANJUTAN)",
                "PEMBANGUNAN BENDUNGAN SEPAKU SEMOI",
                "PEMBANGUNAN BENDUNGAN SIDAN (LANJUTAN)",
                "PEMBANGUNAN JALAN TOL IKN SEGMEN KARANGJOANG - KKT KARIANGAU - IKN",
                "PEMBANGUNAN JALAN TOL PROBOLINGGO - BANYUWANGI PAKET 1",
                "PEMBANGUNAN JEMBATAN NANGA PINOH - ELLA HILIR - BTS KALTENG",
                "PEMBANGUNAN RUSUN ASN 4 - IKN",
                "PEMBANGUNAN SARANA PENGENDALIAN BANJIR SUNGAI BOGOWONTO",
                "PEMBANGUNAN TEMPAT PENGOLAHAN SAMPAH TERPADU - IKN",
                "PENANGANAN BANJIR SUNGAI SEPAKU - IKN",
                "PENATAAN SUMBU KEBANGSAAN TAHAP 1 - IKN",
                "PENATAAN SUMBU KEBANGSAAN TAHAP 2 - IKN",
                "PERUMAHAN KUPANG",
                "POOL ALAT SUBANG",
                "PT ANANDA PRATAMA (BENDUNGAN JRAGUNG)",
                "PT ANANDA PRATAMA (BENDUNGAN SIDAN LANJUTAN)",
                "PT BUMI GRESIK (BENDUNGAN BUDONG - BUDONG)",
                "PT BUMI GRESIK (BENDUNGAN MBAY)",
                "PT BUMI KARSA (PROYEK BULANGO ULU PAKET 3)",
                "PT BUMI KARSA (PROYEK PRESERVASI RUAS JALAN BILUHU BARAT)",
                "PT BUMI SEKAR INDAH (BENDUNGAN MBAY)",
                "PT DINAR MULIA BARAKAH (BENER PAKET 1)",
                "PT GODAM SOLUSI (BENDUNGAN SIDAN LANJUTAN)",
                "PT GRI (GENANGAN CIPANAS)",
                "PT JATI KENCANA (BENDUNGAN JRAGUNG)",
                "PT KARSA PILAR KONSTRUKSI (PERUMAHAN KUPANG)",
                "PT PARAHITA DANAPATI (BENER PAKET 1)",
                "PT RINDU IDOLA SEMESTA (BENER PAKET 4)",
                "PT SAMA ROKHMA INDONESIA (BULANGO ULU)",
                "PT TRANSSINDO HUTAMA KARYA (BENER PAKET 4)",
                "REHABILITASI DAN REKONSTRUKSI GEDUNG DPRD MAMUJU",
                "RS ABDI WALUYO IKN",
                "RS DJUANSIH MAJALENGKA",
                "RUMAH SAKIT UPT VERTIKAL PAPUA",
                "SPAM PAKET 1 - IKN",
                "SPAM PAKET 2 - IKN",
                "WING 2 PUPR IKN (GEDUNG PUPR 5 TOWER)",
            ];

        foreach ( $proyek as $nama )
        {
            Proyek::firstOrCreate ( [ 'nama' => $nama ] );
        }
    }
}

