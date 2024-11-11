<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MasterDataSupplier;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class MasterDataSupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run ()
    {
        $companies = [ 
            "CV Cahaya Berkah Sentosa",
            "PT Xpresindo Logistik Utama",
            "CV Makindo Wiguna",
            "CV Daya Motor Ii",
            "PT Dayton Motor Bali",
            "CV Sawitri Cahaya Traktor",
            "PT Makmur Persada Solusindo",
            "PT Maju Megah Trans",
            "PT Adhie Usaha Mandiri",
            "PT Multicrane Perkasa",
            "CV Kencana Multindo Putra",
            "PT Mukti Abadi Sarana",
            "PT Nihon Pandu Dayatama",
            "PT Centra Global Indo",
            "PT United Tractors Tbk",
            "PT Cahaya Surya Kaltara",
            "CV Cahyadi Sukses Bersama",
            "PT Diesel Utama Indonesia",
            "CV Kurnia Partindo Jaya",
            "PT Gala Jaya Banjarmasin",
            "CV Sinar Makmur Baru",
            "PT Blessindo Prima Sarana",
            "PT Sefas Keliantama",
            "PT Arjuna Logistik Indonesia",
            "CV Industrialindo",
            "CV Geronimo Mandiri",
            "PT Gala Jaya Mandiri",
            "PT Annapurna Jaya Agung",
            "PT Gala Jaya Pekanbaru",
            "PT Diva Mandiri Semesta",
            "PT Mahkota Elang Internusa",
            "PT Bukaka Teknik Utama Tbk",
            "PT Trakindo Utama",
            "CV Harapan Motor",
            "PT Equipindo Perkasa",
            "PT Sicoma Indo Perkasa",
            "PT Vakamindo Mitra Prima",
            "PT Hartono Raya Motor",
            "UD Yoko Motor"
        ];

        foreach ( $companies as $company )
        {
            // Menggunakan factory untuk setiap nama perusahaan
            MasterDataSupplier::factory ()->create ( [ 'nama' => $company ] );
        }
    }
}
