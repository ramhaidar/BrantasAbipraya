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
                "PEMBANGUNAN BENDUNGAN KEUREUTO ACEH",
                "IRIGASI D.I. LEMATANG KOTA PAGAR ALAM PHASE II PAKET 1",
                "PEKERJAAN RANCANG BANGUN RUMAH SUSUN STASIUN TANJUNG BARAT JAKARTA TIMUR",
                "HUNIAN TETAP PASCA BENCANA GEMPA CIANJUR TAHAP 3",
                "PEMBANGUNAN BENDUNGAN BENER PAKET 1 (MYC)",
                "PEMBANGUNAN BENDUNGAN BENER PAKET 4 (MYC)",
                "PT PARAHITA DANAPATI (BENER PAKET 1)",
                "PT RINDU IDOLA SEMESTA (BENER PAKET 4)",
                "PEMBANGUNAN BENDUNGAN JRAGUNG PAKET III",
                "PEMBANGUNAN SARANA PENGENDALIAN BANJIR SUNGAI BOGOWONTO",
                "PEMBANGUNAN JALAN TOL PROBOLINGGO - BANYUWANGI PAKET 1",
                "PEMBANGUNAN BENDUNGAN SIDAN (LANJUTAN)",
                "PT ANANDA PRATAMA (BENDUNGAN SIDAN LANJUTAN)",
                "JARINGAN IRIGASI D.I. BINTANG BANO",
                "PT KARSA PILAR KONSTRUKSI (PERUMAHAN KUPANG)",
                "PEMBANGUNAN BENDUNGAN MBAY",
                "PT BUMI SEKAR INDAH (BENDUNGAN MBAY)",
                "PT GRI (GENANGAN CIPANAS)",
                "PEMBANGUNAN RUSUN ASN 4 - IKN",
                "EMBUNG KIPP - IKN",
                "PEMBANGUNAN BENDUNGAN SEPAKU SEMOI",
                "PEMBANGUNAN BENDUNGAN SEPAKU SEMOI (LANJUTAN)",
                "PENANGANAN BANJIR SUNGAI SEPAKU - IKN",
                "SPAM PAKET 1 - IKN",
                "SPAM PAKET 2 - IKN",
                "PENATAAN SUMBU KEBANGSAAN TAHAP 1 - IKN",
                "PENATAAN SUMBU KEBANGSAAN TAHAP 2 - IKN",
                "JALAN BEBAS HAMBATAN SEKSI 6B - IKN",
                "PEMBANGUNAN JALAN TOL IKN SEGMEN KARANGJOANG - KKT KARIANGAU - IKN",
                "JALAN KERJA KAWASAN SUB BWP 1B 1C - IKN",
                "PEMBANGUNAN TEMPAT PENGOLAHAN SAMPAH TERPADU - IKN",
                "PEMBANGUNAN JEMBATAN NANGA PINOH - ELLA HILIR - BTS KALTENG",
                "BENDUNGAN BUDONG - BUDONG",
                "PEMBANGUNAN BENDUNGAN BULANGO ULU",
                "PT SAMA ROKHMA INDONESIA (BULANGO ULU)",
                "JARINGAN IRIGASI D.I. BALIASE",
                "REHABILITASI DAN REKONSTRUKSI GEDUNG DPRD MAMUJU",
                "RUMAH SAKIT UPT VERTIKAL PAPUA",
                "ELEVATED KARAWANG",
                "POOL ALAT SUBANG",
                "PABRIK SUBANG",
                "OSP IKN",
                "PT BUMI GRESIK (BENDUNGAN BUDONG - BUDONG)",
                "PT BUMI GRESIK (BENDUNGAN MBAY)",
                "PT ANANDA PRATAMA (BENDUNGAN JRAGUNG)",
                "INSTALASI PENYEDIA AIR BERSIH (IPA - IKN)",
                "WING 2 PUPR IKN (GEDUNG PUPR 5 TOWER)",
                "RS ABDI WALUYO IKN",
                "BENDUNGAN MARANGKAYU",
                "RS DJUANSIH MAJALENGKA",
                "PT GODAM SOLUSI (BENDUNGAN SIDAN LANJUTAN)",
                "PT JATI KENCANA (BENDUNGAN JRAGUNG)",
                "EMBUNG LANJUTAN KIPP - IKN",
                "JALAN LINGKAR SEPAKU",
                "PERUMAHAN KUPANG",
                "PT DINAR MULIA BARAKAH (BENER PAKET 1)",
                "PT TRANSSINDO HUTAMA KARYA (BENER PAKET 4)",
                "CV NURA SAKTI (BENER 4)",
                "PT BUMI KARSA (PROYEK BULANGO ULU PAKET 3)",
                "MODULAR TNI IKN",
                "PT BUMI KARSA (PROYEK PRESERVASI RUAS JALAN BILUHU BARAT)"
            ];

        foreach ( $proyek as $nama )
        {
            Proyek::factory ()->create ( [ 'nama' => $nama ] );
        }
    }
}

