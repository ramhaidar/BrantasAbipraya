<?php

namespace Database\Seeders;

use App\Models\Proyek;
use App\Models\AlatProyek;
use App\Models\MasterDataAlat;
use Illuminate\Database\Seeder;
use App\Models\KategoriSparepart;
use App\Models\MasterDataSupplier;
use App\Models\MasterDataSparepart;

class _KeureutoSeeder extends Seeder
{
    public function run () : void
    {
        // Define Alat data with all equipment codes
        $alatCodes = [];

        // Define Sparepart from Panjar
        $sparepartPanjar = [];

        $sparepartNilaiRiil = [ 
            // CV Sawitri Cahaya Traktor 
            [ 'kode' => 'A11', 'supplier' => 'CV Sawitri Cahaya Traktor', 'nama' => 'Adaptor Tooth Bucket', 'part_number' => '-' ],
            [ 'kode' => 'B11', 'supplier' => 'CV Sawitri Cahaya Traktor', 'nama' => 'Filter Oil', 'part_number' => 'UOC 358/207-62-74650' ],
            [ 'kode' => 'B12', 'supplier' => 'CV Sawitri Cahaya Traktor', 'nama' => 'Filter Solar', 'part_number' => 'FC 1005/207-62-71450' ],
            [ 'kode' => 'B12', 'supplier' => 'CV Sawitri Cahaya Traktor', 'nama' => 'Filter Solar', 'part_number' => '600-319-3750' ],
            [ 'kode' => 'A12', 'supplier' => 'CV Sawitri Cahaya Traktor', 'nama' => 'Front Idler Assy', 'part_number' => '20Y-30-00640' ],
            [ 'kode' => 'B12', 'supplier' => 'CV Sawitri Cahaya Traktor', 'nama' => 'Fuel Filter', 'part_number' => 'FC 1003' ],
            [ 'kode' => 'B12', 'supplier' => 'CV Sawitri Cahaya Traktor', 'nama' => 'Fuel Pre Filter', 'part_number' => '10044302/J8620070' ],
            [ 'kode' => 'A11', 'supplier' => 'CV Sawitri Cahaya Traktor', 'nama' => 'Pin Tooth Bucket', 'part_number' => 'STD' ],
            [ 'kode' => 'A11', 'supplier' => 'CV Sawitri Cahaya Traktor', 'nama' => 'Seal Kit Control Valve', 'part_number' => '-' ],
            [ 'kode' => 'A4', 'supplier' => 'CV Sawitri Cahaya Traktor', 'nama' => 'Seal Kit Swivel Joint', 'part_number' => '703-08-33730' ],
            [ 'kode' => 'A11', 'supplier' => 'CV Sawitri Cahaya Traktor', 'nama' => 'Tooth Bucket', 'part_number' => 'STD' ],

            // PT Centra Global Indo
            [ 'kode' => 'A12', 'supplier' => 'PT Centra Global Indo', 'nama' => 'Bolt', 'part_number' => '20Y-30-11340' ],
            [ 'kode' => 'A12', 'supplier' => 'PT Centra Global Indo', 'nama' => 'Carrier Roller Assembly', 'part_number' => '20Y-30-00670' ],
            [ 'kode' => 'C1', 'supplier' => 'PT Centra Global Indo', 'nama' => 'Contact Cleaner', 'part_number' => '-' ],
            [ 'kode' => 'B11', 'supplier' => 'PT Centra Global Indo', 'nama' => 'Filter Oli', 'part_number' => 'P502364' ],
            [ 'kode' => 'B12', 'supplier' => 'PT Centra Global Indo', 'nama' => 'Fuel Filter', 'part_number' => 'FC-1005' ],
            [ 'kode' => 'C1', 'supplier' => 'PT Centra Global Indo', 'nama' => 'Kawat Las LB 52', 'part_number' => '3,2 mm' ],
            [ 'kode' => 'C1', 'supplier' => 'PT Centra Global Indo', 'nama' => 'Kawat Las LB 52', 'part_number' => '4 mm' ],
            [ 'kode' => 'C1', 'supplier' => 'PT Centra Global Indo', 'nama' => 'Pompa Grease', 'part_number' => '-' ],
            [ 'kode' => 'A11', 'supplier' => 'PT Centra Global Indo', 'nama' => 'Seal Kit Bucket', 'part_number' => '-' ],
            [ 'kode' => 'C1', 'supplier' => 'PT Centra Global Indo', 'nama' => 'Tabung Tangki Minyak Rem', 'part_number' => '-' ],
            [ 'kode' => 'A12', 'supplier' => 'PT Centra Global Indo', 'nama' => 'Track Roller Assembly', 'part_number' => '20Y-30-07300' ],
            [ 'kode' => 'A12', 'supplier' => 'PT Centra Global Indo', 'nama' => 'Track Roller Double Flange', 'part_number' => '5802407' ],
            [ 'kode' => 'A12', 'supplier' => 'PT Centra Global Indo', 'nama' => 'Track Roller Single Flange', 'part_number' => '5802406' ],

            // PT Duta Utama Sumatera
            [ 'kode' => 'A2', 'supplier' => 'PT Duta Utama Sumatera', 'nama' => 'Bearing Ball', 'part_number' => '6732-61-3420' ],

            // PT Gala Jaya Pekanbaru
            [ 'kode' => 'A10', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'Absorber Assy, Shock FR', 'part_number' => '48500-EW010' ],
            [ 'kode' => 'A11', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'Baut Roda Belakang Kiri', 'part_number' => '-' ],
            [ 'kode' => 'A11', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'Baut Roda Depan Kanan', 'part_number' => '-' ],
            [ 'kode' => 'A11', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'Baut Stud Per', 'part_number' => '48247-EV050' ],
            [ 'kode' => 'A11', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'Bearing Bush / Assy dia 45', 'part_number' => 'A4003260364' ],
            [ 'kode' => 'A11', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'Bearing Roda Belakang Dalam', 'part_number' => 'SZ366-90010' ],
            [ 'kode' => 'A11', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'Bearing Roda Belakang Luar', 'part_number' => 'SZ366-85011' ],
            [ 'kode' => 'A11', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'BOLT DAN NUT', 'part_number' => '-' ],
            [ 'kode' => 'A11', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'BOLT dan NUT CUTTING', 'part_number' => '7403204 - 7000904' ],
            [ 'kode' => 'A12', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'BOLT DAN NUT SHOE', 'part_number' => '-' ],
            [ 'kode' => 'A11', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'Bolt Ding Dong Panjang', 'part_number' => 'SZ101-18053' ],
            [ 'kode' => 'A11', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'Bolt Ding Dong Pendek', 'part_number' => 'SZ101-18012' ],
            [ 'kode' => 'A11', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'BOLT NUT SEKMEN TRACK', 'part_number' => '7414161' ],
            [ 'kode' => 'A11', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'Break Lining/Set (STD) Construction', 'part_number' => 'A4004212130' ],
            [ 'kode' => 'A12', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'Carrier Roller', 'part_number' => '207-30-00551' ],
            [ 'kode' => 'B12', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'Cartridge', 'part_number' => '6742-01-4540/J8619009' ],
            [ 'kode' => 'B12', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'Cartridge, Assembly', 'part_number' => '600-319-3610' ],
            [ 'kode' => 'B12', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'Cartridge, Fuel Filter', 'part_number' => '600-319-3750/J8620750' ],
            [ 'kode' => 'A11', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'Clutch Release Bearing', 'part_number' => 'TK70-1A1' ],
            [ 'kode' => 'A11', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'CUTTING END', 'part_number' => '9033812' ],
            [ 'kode' => 'A11', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'CUTTING END', 'part_number' => '9033813' ],
            [ 'kode' => 'A11', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'Cylinder Assy + Spring + Yoge', 'part_number' => '207-30-71441' ],
            [ 'kode' => 'A2', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'Diaphragm / Kit B/73', 'part_number' => 'QA4004260143' ],
            [ 'kode' => 'A11', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'End Assy, Tie Rod LH', 'part_number' => 'S4550-E0090' ],
            [ 'kode' => 'A11', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'End Assy, Tie Rod RH', 'part_number' => 'S4540-E0090' ],
            [ 'kode' => 'B12', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'Filter Rakor', 'part_number' => '2040TM-OR/T000 344 3286' ],
            [ 'kode' => 'A13', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'Floating Seal Assembly', 'part_number' => '207-27-00310' ],
            [ 'kode' => 'C1', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'Flywheel', 'part_number' => '13450-E0861' ],
            [ 'kode' => 'B12', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'Fuel Filter', 'part_number' => 'A4000920005' ],
            [ 'kode' => 'B12', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'Fuel Filter', 'part_number' => 'P50-5961' ],
            [ 'kode' => 'B12', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'Fuel Filter', 'part_number' => 'FC1301' ],
            [ 'kode' => 'B12', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'Fuel Water Spaator', 'part_number' => '23401-1440L' ],
            [ 'kode' => 'A11', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'Hex Screw MBN 10105 Baut Dingdong', 'part_number' => 'N000000005731' ],
            [ 'kode' => 'A11', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'Hex Screw MBN 10105 Baut Dingdong', 'part_number' => 'N000000005729' ],
            [ 'kode' => 'C1', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'HEX SCREW MBN 10105 -M14 X1,5 X 50 10.9', 'part_number' => 'N000000005563' ],
            [ 'kode' => 'A11', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'Hexagon Nut En 14218-M20X1,5-10 DBL 9', 'part_number' => 'N910112020000' ],
            [ 'kode' => 'A11', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'Hexagon Screw Iso 8765-M24X2X150-10.9', 'part_number' => 'N000000005714' ],
            [ 'kode' => 'A11', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'Hose ( BUCKET )', 'part_number' => '20Y-62-13412' ],
            [ 'kode' => 'A11', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'Hose ( BUCKET )', 'part_number' => '22U-62-34611' ],
            [ 'kode' => 'A7', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'Hose ( HOSE ARM )', 'part_number' => '20Y-62-53750' ],
            [ 'kode' => 'A3', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'Hose Travel', 'part_number' => '2A5-62-11191' ],
            [ 'kode' => 'A3', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'Hose Travel', 'part_number' => '2A5-62-11211' ],
            [ 'kode' => 'A3', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'Hose Travel', 'part_number' => '02774-00419' ],
            [ 'kode' => 'A3', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'Hose Travel', 'part_number' => '02774-00220' ],
            [ 'kode' => 'A7', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'Hose, Flange Type ( HOSE ARM )', 'part_number' => '07074-006A7' ],
            [ 'kode' => 'A2', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'Insulator Sub Assy,Engine MT RR', 'part_number' => 'S1206-EW030' ],
            [ 'kode' => 'A7', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'Kabel Dump', 'part_number' => '6M' ],
            [ 'kode' => 'A11', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'Kabel Transmisi Mundur/Cable Assy', 'part_number' => '33830-EW031' ],
            // [ 'kode' => 'A11', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'Kabel Transmisi Mundur/Cable Assy', 'part_number' => '33830-ew031' ], // Duplicate but dont remove from code Please
            [ 'kode' => 'A9', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'Kampas Rem Belakang + Paku', 'part_number' => '-' ],
            [ 'kode' => 'A11', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'Karet Ding Dong', 'part_number' => '-' ],
            [ 'kode' => 'A11', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'Kuku Macan', 'part_number' => '1 Set unt 1 Mobil' ],
            [ 'kode' => 'C1', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'Lampu H1', 'part_number' => '24V - 70W' ],
            [ 'kode' => 'C1', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'Lampu H11', 'part_number' => '24V - 70W' ],
            [ 'kode' => 'C1', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'Lampu H3', 'part_number' => '24V - 70W' ],
            [ 'kode' => 'C1', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'Lampu H7', 'part_number' => '24V - 70W' ],
            [ 'kode' => 'A11', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'Leaf Sub Assy , FR Spring No.5', 'part_number' => 'S4805-EV040' ],
            [ 'kode' => 'C1', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'Leaf Sub Assy , FR Spring No.6', 'part_number' => 'S4806-EV040' ],
            [ 'kode' => 'C1', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'Locknut - M80x1.5', 'part_number' => 'A9763560026' ],
            [ 'kode' => 'A3', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'Lu Clutch Plate', 'part_number' => 'QA4002501303' ],
            [ 'kode' => 'A2', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'LU Engine Support/RR/TT', 'part_number' => 'A4002401118' ],
            [ 'kode' => 'C1', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'LU Presure Sensor /KB', 'part_number' => 'A4005420718' ],
            [ 'kode' => 'A2', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'LU Stater / 24V Stater Motor', 'part_number' => 'A4001500901' ],
            [ 'kode' => 'A8', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'Lu Tie Rod', 'part_number' => 'A4003301703' ],
            // [ 'kode' => 'A11', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'Lu Tie Rod', 'part_number' => 'A4003301703' ], // Wrong Kode but dont remove from code Please
            [ 'kode' => 'A3', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'Lu Universal Joint / Kit', 'part_number' => 'A4004111011' ],
            [ 'kode' => 'C1', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'MATA GERINDA TANGAN', 'part_number' => '-' ],
            [ 'kode' => 'B28', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'Minyak Rem', 'part_number' => 'BOTOL BESAR' ],
            [ 'kode' => 'B28', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'Minyak Rem', 'part_number' => '-' ],
            [ 'kode' => 'A11', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'Nut N 13023', 'part_number' => 'N000000005740' ],
            [ 'kode' => 'B11', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'Oil Filter', 'part_number' => 'C1316' ],
            [ 'kode' => 'B11', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'Oil Filter Element', 'part_number' => 'E161H01D28/A40018440202' ],
            [ 'kode' => 'B22', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'Oli  Hydraulic', 'part_number' => 'SAE 10' ],
            [ 'kode' => 'C1', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'Packing Tutup Klep Hino Lohan', 'part_number' => '11213-1880' ],
            [ 'kode' => 'A11', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'Pin Spring Depan', 'part_number' => '48112-EW010' ],
            [ 'kode' => 'A11', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'Pin Spring Depan', 'part_number' => '48423-EW020' ],
            [ 'kode' => 'B12', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'Pre Fuel Filter', 'part_number' => 'A4004701669/A4004770702' ],
            [ 'kode' => 'A11', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'Radbefestigungsbolzen', 'part_number' => 'A4004010871' ],
            [ 'kode' => 'C1', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'Rectifier Bridge F Three Phase Alternator', 'part_number' => 'A4001540016' ],
            [ 'kode' => 'A11', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'Regulator Switch', 'part_number' => 'A4001540006' ],
            [ 'kode' => 'C1', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'Repair Kit Ball Joint V', 'part_number' => 'A0003503605' ],
            [ 'kode' => 'A11', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'Repair Kit Booter Assy Clutch Hino', 'part_number' => '-' ],
            [ 'kode' => 'A11', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'Repair Kit Clutch Master Kopling Bawah', 'part_number' => '220-81581' ],
            [ 'kode' => 'A11', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'Rod, Steering Tie', 'part_number' => '45461-E0030' ],
            [ 'kode' => 'A11', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'Rubber Bearing Etal Bushing, Cylindrica', 'part_number' => 'A0003238185' ],
            [ 'kode' => 'A11', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'Rubber Bearing FA', 'part_number' => 'A0003223285' ],
            [ 'kode' => 'A7', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'SEAL ARM', 'part_number' => '-' ],
            [ 'kode' => 'A7', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'SEAL BOOM', 'part_number' => '-' ],
            [ 'kode' => 'A11', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'SEAL BUCKET', 'part_number' => '-' ],
            [ 'kode' => 'A11', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'Seal Debu', 'part_number' => '-' ],
            [ 'kode' => 'A11', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'Seal Kit Adjuster', 'part_number' => 'PC 300' ],
            [ 'kode' => 'A11', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'Seal Rem', 'part_number' => 'SC80208' ],
            // [ 'kode' => 'A11', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'Seal Rem', 'part_number' => 'SC80209' ], // Salah seharusnya A9, Jangan Hapus Line Ini
            [ 'kode' => 'A9', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'Seal Rem', 'part_number' => 'SC80209' ],
            [ 'kode' => 'A11', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'Seal Roda Belakang Dalam', 'part_number' => '-' ],
            [ 'kode' => 'A11', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'Seal Roda Belakang Luar', 'part_number' => '-' ],
            [ 'kode' => 'C1', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'Sealing Ring / Major Seal Kit', 'part_number' => 'A4004630260' ],
            [ 'kode' => 'C1', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'Sechskantschr Iso 8765-M24X2X180-10.9 ( Baut )', 'part_number' => 'N000000005503' ],
            [ 'kode' => 'A12', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'SHOE', 'part_number' => '-' ],
            [ 'kode' => 'A2', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'Spider Kit, Universal Joint', 'part_number' => '04371-E1260' ],
            [ 'kode' => 'A11', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'Spring Belakang No.3', 'part_number' => '48213-EV050' ],
            [ 'kode' => 'A11', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'Spring Leaf 1 25T Rear', 'part_number' => 'A4003240111' ],
            [ 'kode' => 'A11', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'Spring Leaf 1 Front', 'part_number' => 'A4003210411' ],
            [ 'kode' => 'A11', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'Spring Leaf 1 Front', 'part_number' => 'A4003210411' ],
            [ 'kode' => 'A11', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'Spring Leaf 5 25T Rear', 'part_number' => 'A4003210115' ],
            [ 'kode' => 'A11', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'Tapered Roller Bearing / 130X80X37', 'part_number' => '3116/Q / A4009811805' ],
            [ 'kode' => 'A11', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'Tapered Roller Bearing / 150X100X31', 'part_number' => '33020 9 X 025 / A4009811905' ],
            [ 'kode' => 'A11', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'Tapered Roller Bearing/90X50X32', 'part_number' => 'A4009811605' ],
            [ 'kode' => 'C1', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'TAPPET SWITCH', 'part_number' => 'A0015457309' ],
            [ 'kode' => 'A11', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'U-Bolt', 'part_number' => 'A4003310125' ],
            [ 'kode' => 'C1', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'V Belt', 'part_number' => 'A4009930096' ],
            [ 'kode' => 'A11', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'Wheel Mounting Bolt/HDT/M22X2.5', 'part_number' => 'A4004012071' ],
            [ 'kode' => 'A3', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'Wheel Nut', 'part_number' => 'QA4004010972' ],
            [ 'kode' => 'C1', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'Wheel Nut', 'part_number' => 'QA 400 401 09 72' ],
            [ 'kode' => 'A11', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'Wheel Nut', 'part_number' => 'A4004010972' ],
            [ 'kode' => 'A2', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'Yoke Sub Assy,Universal Joint Shaft', 'part_number' => 'S370L-EW030+37302-EW020' ],
            [ 'kode' => 'A2', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'ZB Engine Support LH/Without rame Inser', 'part_number' => 'A4002400217' ],
            [ 'kode' => 'A2', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'ZB Engine Support RH/Without rame Inser', 'part_number' => 'A4002400317' ],
            [ 'kode' => 'C1', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'ZB Gearshift Linkage/G131', 'part_number' => 'A4002600189' ],
            [ 'kode' => 'C1', 'supplier' => 'PT Gala Jaya Pekanbaru', 'nama' => 'ZB Water Pump', 'part_number' => 'A9042002501' ],

            // PT Multicrane Perkasa
            // [ 'kode' => 'B11', 'supplier' => 'PT Multicrane Perkasa', 'nama' => 'Filter Oil', 'part_number' => '10297295' ], // Duplicate but dont remove from code Please
            [ 'kode' => 'B11', 'supplier' => 'PT Multicrane Perkasa', 'nama' => 'Filter Oli', 'part_number' => '10297295' ],
            [ 'kode' => 'B12', 'supplier' => 'PT Multicrane Perkasa', 'nama' => 'Fuel Fine Filter', 'part_number' => '12820742' ],
            [ 'kode' => 'B12', 'supplier' => 'PT Multicrane Perkasa', 'nama' => 'Fuel Fine Filter', 'part_number' => '10429946' ],
            [ 'kode' => 'B12', 'supplier' => 'PT Multicrane Perkasa', 'nama' => 'Fuel Fine Filter', 'part_number' => '12820742 / 10429946' ],
            [ 'kode' => 'B12', 'supplier' => 'PT Multicrane Perkasa', 'nama' => 'Fuel Fine Filter', 'part_number' => '12820742' ],
            [ 'kode' => 'A2', 'supplier' => 'PT Multicrane Perkasa', 'nama' => 'V BELT', 'part_number' => '11005218' ],

            // PT United Tractors 
            [ 'kode' => 'A11', 'supplier' => 'PT United Tractors', 'nama' => 'Piston Shoe', 'part_number' => '706-7G-41410' ],
        ];

        // Extract unique suppliers from sparepartNilaiRiil
        $supplierNilaiRiil = [];
        foreach ( $sparepartNilaiRiil as $part )
        {
            if ( ! in_array ( $part[ 'supplier' ], $supplierNilaiRiil ) )
            {
                $supplierNilaiRiil[] = $part[ 'supplier' ];
            }
        }

        // Extract unique suppliers from sparepartPanjar
        $supplierPanjar = [];
        foreach ( $sparepartPanjar as $part )
        {
            if ( ! in_array ( $part[ 'supplier' ], $supplierPanjar ) )
            {
                $supplierPanjar[] = $part[ 'supplier' ];
            }
        }

        // Create MasterDataSupplier records for Panjar suppliers
        foreach ( $supplierPanjar as $supplier )
        {
            // Use firstOrCreate to make idempotent
            MasterDataSupplier::firstOrCreate (
                [ 'nama' => $supplier ],
                [ 
                    'alamat'         => "-",
                    'contact_person' => "- (-)",
                ]
            );
        }

        $suppliers        = array_merge ( $supplierNilaiRiil, $supplierPanjar );
        $missingSuppliers = [];

        // Verify all suppliers exist
        foreach ( $suppliers as $supplierName )
        {
            if ( ! MasterDataSupplier::where ( 'nama', $supplierName )->exists () )
            {
                $missingSuppliers[] = $supplierName;
            }
        }

        if ( ! empty ( $missingSuppliers ) )
        {
            console ( "Missing suppliers: " . implode ( ', ', $missingSuppliers ) );
            return;
        }

        $spareparts = array_merge ( $sparepartPanjar, $sparepartNilaiRiil );

        foreach ( $spareparts as $sparepart )
        {
            // Find kategori
            $kategori = KategoriSparepart::where ( 'kode', $sparepart[ 'kode' ] )->first ();
            if ( ! $kategori ) continue;

            // Find supplier
            $supplier = MasterDataSupplier::where ( 'nama', $sparepart[ 'supplier' ] )->first ();
            if ( ! $supplier )
            {
                console ( "Supplier not found: " . $sparepart[ 'supplier' ] );
                continue;
            }

            // Use firstOrCreate to avoid duplicate entries
            $sparepart = MasterDataSparepart::firstOrCreate (
                [ 
                    'nama'        => $sparepart[ 'nama' ],
                    'part_number' => $sparepart[ 'part_number' ] ?? '-'
                ],
                [ 
                    'merk'                  => '-',
                    'id_kategori_sparepart' => $kategori->id
                ]
            );

            // Link supplier to sparepart if not already linked
            if ( ! $sparepart->masterDataSuppliers ()->where ( 'master_data_supplier.id', $supplier->id )->exists () )
            {
                $sparepart->masterDataSuppliers ()->attach ( $supplier->id );
            }
        }

        // Link equipment to Budong Budong project
        $proyek = Proyek::where ( 'nama', 'PEMBANGUNAN BENDUNGAN KEUREUTO ACEH' )->first ();
        if ( ! $proyek )
        {
            console ( "Project 'PEMBANGUNAN BENDUNGAN KEUREUTO ACEH' not found!" );
            return;
        }

        // Find existing equipment
        $existingAlats = MasterDataAlat::whereIn ( 'kode_alat', $alatCodes )->get ();

        // Find missing equipment codes
        $existingCodes = $existingAlats->pluck ( 'kode_alat' )->toArray ();
        $missingCodes  = array_diff ( $alatCodes, $existingCodes );

        if ( count ( $missingCodes ) > 0 )
        {
            console ( "Missing equipment codes: " . implode ( ', ', $missingCodes ) );
        }

        // Process each equipment
        foreach ( $existingAlats as $alat )
        {
            if ( AlatProyek::where ( 'id_proyek', $proyek->id )->where ( 'id_master_data_alat', $alat->id )->whereNull ( 'removed_at' )->first () )
            {
                continue;
            }

            try
            {
                // Link equipment to project
                $alatProyek = AlatProyek::create ( [ 
                    'id_proyek'           => $proyek->id,
                    'id_master_data_alat' => $alat->id,
                    'assigned_at'         => now (),
                    'removed_at'          => null
                ] );

                // Update the current project for the equipment
                $alat->update ( [ 
                    'id_proyek_current' => $proyek->id
                ] );
            }
            catch ( \Exception $e )
            {
                console ( "ERROR linking equipment: " . $e->getMessage () );
            }
        }
    }
}
