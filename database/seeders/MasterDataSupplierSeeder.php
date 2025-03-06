<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MasterDataSupplier;
use App\Models\LinkSupplierSparepart;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class MasterDataSupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run ()
    {
        $suppliers = [ 
            [ "Cardig", "Bram", "+62817852629", "Jakarta" ],
            [ "CV Cahaya Berkah Sentosa", "Slamet Khambali", "+6285257548948", "Sidoarjo" ],
            [ "CV Cahyadi Sukses Bersama", "Alvin Cahyadi", "+6281288932388", "Tarakan" ],
            [ "CV Daya Motor II", "Hartopo", "+62812561142", "Kalimantan Barat" ],
            [ "CV Geronimo Mandiri", "Lina", "+6281330445566", "Surabaya" ],
            [ "CV Harapan Motor", "Jetson", "+62811570776", "Kalimantan Barat" ],
            [ "CV Industrialindo", "Haris Frianto", "+6281252066050", "Makassar" ],
            [ "CV Kencana Multindo Putra", "Arif", "+6281382333036", "Jakarta" ],
            [ "CV Kurnia Partindo Jaya", "Jonsen", "+6281938257229", "Surabaya" ],
            [ "CV Makindo Wiguna", "Bogan", "+6289630805019", "Jakarta" ],
            [ "CV Mitra Mandiri Teknik", "Alvian", "+6283822259968", "Jakarta" ],
            [ " CV Sawitri Cahaya Traktor", "-", "-", "-" ],
            [ "CV Sinar Makmur Baru", "Adi", "+6285730063480", "Surabaya" ],
            [ "CV Tunggal Jaya", "Suryadi M", "+6285237759999", "Ende" ],
            [ "Eko Juhan", "Eko Juhan", "+6281288199845", "Jakarta" ],
            [ "PT Adhie Usaha Mandiri", "Ringo", "+6281383801009", "Bekasi" ],
            [ "PT Angkasa Pura Logistik", "Redy", "+6281133899928", "Jakarta" ],
            [ "PT Arjuna Logistik Indonesia", "Ismed", "+6281334666996", "Jakarta" ],
            [ "PT Armex Tiga Serangkai", "Rika", "+6281399880292", "Jakarta" ],
            [ "PT Batch Automation Indonesia", "Afri", "+6281572279826", "Jakarta" ],
            [ "PT Bhaskoro Sukses Transportir", "Yuni", "+6281216279696", "Jakarta" ],
            [ "PT Blessindo Prima Sarana", "Mulya", "+628111202119", "Jakarta" ],
            [ "PT Bukaka Teknik Utama", "Adli", "+6282112255267", "Jakarta" ],
            [ "PT Cahaya Surya Kaltara", "Alvin Cahyadi", "+6281288932388", "Tarakan" ],
            [ "PT Centra Global Indo", "Segren", "+628116550886", "Medan" ],
            [ "PT Dayton Motor Bali", "Erik", "+6281346393852", "Bali" ],
            [ "PT Detede", "Teddy", "+6285210567648", "Cibubur" ],
            [ "PT Diesel Pratama Indonesia", "Aldy", "+6281386741768", "Jakarta" ],
            [ "PT Diesel Utama", "Ishardinata", "+6281316740026", "Jakarta" ],
            [ "PT Diva Mandiri Semesta", "Ariyani", "+6281213061629", "Ciledug" ],
            [ "PT Duta Utama Sumatera", "-", "-", "-" ],
            [ "PT Equipindo Perkasa", "Syaiful", "+6281213117672", "Jakarta" ],
            [ "PT Eurotruk Transindo", "Rambat", "+6282327594444", "Jakarta" ],
            [ "PT Fortuna Senjaya Abadi", "Hany", "+6285884211159", "Jakarta" ],
            [ "PT Gala Jaya Banjarmasin", "Feble", "+6285390366489", "Banjarmasin" ],
            [ "PT Gala Jaya Mandiri", "Mustika", "+6281258081601", "Kalbar" ],
            [ "PT Gala Jaya Mandiri (Manado)", "Angga Martinus", "+6282351622074", "Manado" ],
            [ "PT Gala Jaya Pekanbaru", "Sri", "+6285265481060", "Pekanbaru" ],
            [ "PT Hartono Raya Motor Denpasar", "Emarianie", "+6281514264192", "Surabaya" ],
            [ "PT Kenzie Tata Utama", "Tumpal", "+628119799188", "Jakarta" ],
            [ "PT LTA Diesel Engine Service", "Rasyidin", "+6281345262171", "Jakarta" ],
            [ "PT Mahkota Elang Internusa", "Acim", "+628999926709", "Jakarta" ],
            [ "PT Maju Megah Trans", "Yoan", "+6285241354293", "Gorontalo" ],
            [ "PT Makmur Persada Solusindo", "Hendra", "+6285277932320", "Jakarta" ],
            [ "PT Mukti Abadi Sarana", "Warsono", "+6281280818115", "Jakarta" ],
            [ "PT Multi Traktor Utama", "-", "-", "-" ],
            [ "PT Multicrane Perkasa", "Ipan", "+6282312000323", "Jakarta" ],
            [ "PT Nihon Pandudayatama", "Sunarto", "+6281288190369", "Jakarta" ],
            [ "PT Quadra Pacific Indonesia", "Sindu", "+628114328820", "Surabaya" ],
            [ "PT Sacindo Machinery", "Mayor Toni", "+6287788398150", "Jakarta" ],
            [ "PT Sacon Indonesia", "Bogan", "+6289630805019", "Jakarta" ],
            [ "PT Sadikun Niagamas Raya", "Feni Fatmawati", "+6281318167752", "Jakarta" ],
            [ "PT Samudera Logistik", "Sonny", "+6281228087452", "Jakarta" ],
            [ "PT Sarana Bakti Internasional", "Lucy", "+628114370311", "Jakarta" ],
            [ "PT Sefas Keliantama", "Abiyyu", "+6281343484540", "Jakarta" ],
            [ "PT Sefas Pelindotama", "Irfan", "+6285691057241", "Kalimantan Timur" ],
            [ "PT Sicoma Indo Perkasa", "Sukma", "+6281386760476", "Jakarta" ],
            [ "PT Sumber Mesin Raya", "Kevin Candra", "+6281289171167", "Jakarta" ],
            [ "PT Surabaya Industrial Estate Rungkut", "Ati", "+6282111350135", "Jakarta" ],
            [ "PT Trakindo Utama", "Astrid", "+628111330856", "Bandung" ],
            [ "PT Tri Berkah Jaya Abadi", "Havidz", "+6281154107090", "Balikpapan" ],
            [ "PT United Tractors", "Restu", "+6285715655554", "Jakarta" ],
            [ "PT Vakamindo Mitra Prima", "Syahrul", "+6281319770019", "Jakarta" ],
            [ "PT Xpresindo Logistik Utama", "Indra", "+6283830795144", "Global" ],
            [ "UD Yoko Motor", "Joseph", "+6281238414500", "Denpasar" ]
        ];

        foreach ( $suppliers as $supplier )
        {
            // Format contact person as "PIC (PhoneNumber)"
            $contact_person = $supplier[ 1 ] . " (" . $supplier[ 2 ] . ")";

            // Use firstOrCreate to make idempotent
            MasterDataSupplier::firstOrCreate (
                [ 'nama' => $supplier[ 0 ] ],
                [ 
                    'alamat'         => $supplier[ 3 ],
                    'contact_person' => $contact_person
                ]
            );
        }
    }
}
