<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\Proyek;
use App\Models\AlatProyek;
use App\Models\MasterDataAlat;
use Illuminate\Database\Seeder;
use App\Models\KategoriSparepart;
use App\Models\MasterDataSupplier;
use Illuminate\Support\Facades\DB;
use App\Models\MasterDataSparepart;
use App\Models\LinkSupplierSparepart;

class _BudongBudongSeeder extends Seeder
{
    public function run () : void
    {
        // Create suppliers
        $suppliers = [ 
            'Sumber Coklat', //
            'Elektronik', //
            'Mattoangin', //
            'Arfan Ban', //
            'Sumber Coklat Teknik', //
            'PT Adhie Usaha Mandiri', //
            'PT Sefas Keliantama', // 
            'PT LTA Diesel Engine Service', // 
            'AI Diesel', // 
            'CV Industrialindo', //
            'PT. Gala Jaya Mandiri', //
            'Sumatra', //
            'Aneka Teknik', //
            'PT Sacon Indonesia' //
        ];

        foreach ( $suppliers as $supplierName )
        {
            MasterDataSupplier::firstOrCreate (
                [ 'nama' => $supplierName ],
                [ 'alamat' => '-', 'contact_person' => '-' ]
            );
        }

        // Create spareparts and link with suppliers
        $data = [ 
            [ 'kode' => 'B11', 'supplier' => 'PT Adhie Usaha Mandiri', 'nama' => 'Engine Oil Filter-12' ],
            [ 'kode' => 'B12', 'supplier' => 'PT Adhie Usaha Mandiri', 'nama' => 'Fuel Pre Filter-12' ],
            [ 'kode' => 'B12', 'supplier' => 'PT Adhie Usaha Mandiri', 'nama' => 'Fuel Filter-12' ],
            [ 'kode' => 'B13', 'supplier' => 'PT Adhie Usaha Mandiri', 'nama' => 'Air Cleaner-12' ],
            [ 'kode' => 'B11', 'supplier' => 'PT Adhie Usaha Mandiri', 'nama' => 'Engine Oil Filter-12' ],
            [ 'kode' => 'B12', 'supplier' => 'PT Adhie Usaha Mandiri', 'nama' => 'Fuel Filter-12' ],
            [ 'kode' => 'B13', 'supplier' => 'PT Adhie Usaha Mandiri', 'nama' => 'Air Cleaner-12' ],
            [ 'kode' => 'A2', 'supplier' => 'PT LTA Diesel Engine Service', 'nama' => 'Injector Assy D6G2-12' ],
            [ 'kode' => 'B21', 'supplier' => 'PT Sefas Keliantama', 'nama' => 'Oli Rimula 15W40-12' ],
            [ 'kode' => 'B22', 'supplier' => 'PT Sefas Keliantama', 'nama' => 'Oli Tellus 68-12' ],
            [ 'kode' => 'A5', 'supplier' => 'AI Diesel', 'nama' => 'Las Pinion Gardan Axor' ],
            [ 'kode' => 'A11', 'supplier' => 'Aneka Teknik', 'nama' => 'Bushing Exca' ],
            [ 'kode' => 'B3', 'supplier' => 'Arfan Ban', 'nama' => 'Ban dalam 10.00-20' ],
            [ 'kode' => 'B3', 'supplier' => 'Arfan Ban', 'nama' => 'Ban dalam 10.00-20' ],
            [ 'kode' => 'B3', 'supplier' => 'CV Industrialindo', 'nama' => 'Ban Luar 17,5-25 12 PR', 'part_number' => 'GT GRIP' ],
            [ 'kode' => 'B23', 'supplier' => 'CV Industrialindo', 'nama' => 'Oli 90', 'part_number' => 'Valvoline 80W90' ],
            [ 'kode' => 'B12', 'supplier' => 'CV Industrialindo', 'nama' => 'Fuel Filter', 'part_number' => '1R-0750' ],
            [ 'kode' => 'B11', 'supplier' => 'CV Industrialindo', 'nama' => 'Oil Filter', 'part_number' => 'Sakura' ],
            [ 'kode' => 'B12', 'supplier' => 'CV Industrialindo', 'nama' => 'Cartridge', 'part_number' => '600-311-8321' ],
            [ 'kode' => 'B12', 'supplier' => 'CV Industrialindo', 'nama' => 'FILTER SOLAR', 'part_number' => '600-311-7460' ],
            [ 'kode' => 'B11', 'supplier' => 'CV Industrialindo', 'nama' => 'FILTER OLI', 'part_number' => '600-211-624' ],
            [ 'kode' => 'B13', 'supplier' => 'CV Industrialindo', 'nama' => 'FILTER AIR/CORROSION', 'part_number' => '600-411-1191' ],
            [ 'kode' => 'B14', 'supplier' => 'CV Industrialindo', 'nama' => 'FILTER HYDROLIK', 'part_number' => '07063-01054' ],
            [ 'kode' => 'B13', 'supplier' => 'CV Industrialindo', 'nama' => 'SARINGAN UDARA LUAR', 'part_number' => 'P522452' ],
            [ 'kode' => 'B13', 'supplier' => 'CV Industrialindo', 'nama' => 'SARINGAN UDARA DALAM', 'part_number' => 'P181191' ],
            [ 'kode' => 'B16', 'supplier' => 'CV Industrialindo', 'nama' => 'Element', 'part_number' => '07063-01054' ],
            [ 'kode' => 'B16', 'supplier' => 'CV Industrialindo', 'nama' => 'Cartridge', 'part_number' => '23S-49-13122' ],
            [ 'kode' => 'B12', 'supplier' => 'CV Industrialindo', 'nama' => 'Filter Solar', 'part_number' => '4587259' ],
            [ 'kode' => 'B11', 'supplier' => 'CV Industrialindo', 'nama' => 'Filter Oli', 'part_number' => '4587260' ],
            [ 'kode' => 'B13', 'supplier' => 'CV Industrialindo', 'nama' => 'Air Cleaner', 'part_number' => 'Perkins' ],
            [ 'kode' => 'B11', 'supplier' => 'CV Industrialindo', 'nama' => 'Filter Oli Mesin', 'part_number' => '15607-2190L' ],
            [ 'kode' => 'B15', 'supplier' => 'CV Industrialindo', 'nama' => 'Filter Oli Transmisi', 'part_number' => '32915-LVA10' ],
            [ 'kode' => 'A7', 'supplier' => 'CV Industrialindo', 'nama' => 'Hose Breaker', 'part_number' => 'Custom' ],
            [ 'kode' => 'B11', 'supplier' => 'CV Industrialindo', 'nama' => 'Seal, Oil', 'part_number' => '703-0700-15080' ],
            [ 'kode' => 'A7', 'supplier' => 'CV Industrialindo', 'nama' => 'Seal, Dust', 'part_number' => '903-08-95770' ],
            [ 'kode' => 'C1', 'supplier' => 'CV Industrialindo', 'nama' => 'Grease', 'part_number' => 'Cobra @ 16 Kg' ],
            [ 'kode' => 'A12', 'supplier' => 'CV Industrialindo', 'nama' => 'BEARING RODA DEPAN DALAM PS125/HDX', 'part_number' => '50KW01' ],
            [ 'kode' => 'A12', 'supplier' => 'CV Industrialindo', 'nama' => 'BEARING RODA DEPAN LUAR PS125/HDX', 'part_number' => '32207' ],
            [ 'kode' => 'A12', 'supplier' => 'CV Industrialindo', 'nama' => 'BEARING RODA BELAKANG DALAM PS125/HDX', 'part_number' => '30212' ],
            [ 'kode' => 'A12', 'supplier' => 'CV Industrialindo', 'nama' => 'BEARING RODA BELAKANG LUAR PS125/HDX', 'part_number' => '30211' ],
            [ 'kode' => 'A12', 'supplier' => 'CV Industrialindo', 'nama' => 'SEAL RODA BELAKANG DALAM PS125/HDX', 'part_number' => 'PS125' ],
            [ 'kode' => 'A12', 'supplier' => 'CV Industrialindo', 'nama' => 'SEAL RODA BELAKANG LUAR PS125/HDX', 'part_number' => 'PS125' ],
            [ 'kode' => 'B12', 'supplier' => 'CV Industrialindo', 'nama' => 'FILTER SOLAR ATAS PS125/HDX', 'part_number' => 'PS125' ],
            [ 'kode' => 'B12', 'supplier' => 'CV Industrialindo', 'nama' => 'FILTER SOLAR BAWAH PS125/HDX', 'part_number' => 'PS125' ],
            [ 'kode' => 'B11', 'supplier' => 'CV Industrialindo', 'nama' => 'FILTER OLI PS125/HDX', 'part_number' => 'PS125' ],
            [ 'kode' => 'B13', 'supplier' => 'CV Industrialindo', 'nama' => 'AIR CLEANER PS125/HDX', 'part_number' => 'PS125' ],
            [ 'kode' => 'A3', 'supplier' => 'CV Industrialindo', 'nama' => 'BEARING PROP. SHAFT+KARET PS125/HDX', 'part_number' => 'PS125' ],
            [ 'kode' => 'A9', 'supplier' => 'CV Industrialindo', 'nama' => 'SEAL REM PS125', 'part_number' => 'PS125' ],
            [ 'kode' => 'A9', 'supplier' => 'CV Industrialindo', 'nama' => 'SEAL DEBU PS125/HDX', 'part_number' => 'PS125' ],
            [ 'kode' => 'A12', 'supplier' => 'CV Industrialindo', 'nama' => 'SEAL RODA DEPAN PS125/HDX', 'part_number' => 'PS125' ],
            [ 'kode' => 'A8', 'supplier' => 'CV Industrialindo', 'nama' => 'TIEROD PS125' ],
            [ 'kode' => 'A3', 'supplier' => 'CV Industrialindo', 'nama' => 'CROSSJOINT PTO PS125', 'part_number' => 'GUDUMP1' ],
            [ 'kode' => 'A3', 'supplier' => 'CV Industrialindo', 'nama' => 'CROSSJOINT PROP. SHAFT PS125/HDX', 'part_number' => 'GUM75' ],
            [ 'kode' => 'A3', 'supplier' => 'CV Industrialindo', 'nama' => 'SEAL KOPLING BAWAH PS125/HDX', 'part_number' => 'PS125' ],
            [ 'kode' => 'A3', 'supplier' => 'CV Industrialindo', 'nama' => 'SEAL KOPLING ATAS PS125/HDX', 'part_number' => 'PS125' ],
            [ 'kode' => 'A2', 'supplier' => 'CV Industrialindo', 'nama' => 'RADIATOR PS125/HDX', 'part_number' => 'PS125' ],
            [ 'kode' => 'A2', 'supplier' => 'CV Industrialindo', 'nama' => 'EXHAUST BRAKE PS125/HDX', 'part_number' => 'PS125' ],
            [ 'kode' => 'A1', 'supplier' => 'CV Industrialindo', 'nama' => 'HANDLE KIRI PS125/HDX', 'part_number' => 'PS125' ],
            [ 'kode' => 'A1', 'supplier' => 'CV Industrialindo', 'nama' => 'LAMPU BELAKANG R/L', 'part_number' => 'PS125' ],
            [ 'kode' => 'A1', 'supplier' => 'CV Industrialindo', 'nama' => 'KACA LAMPU WESER DEPAN R/L', 'part_number' => 'PS125' ],
            [ 'kode' => 'A9', 'supplier' => 'CV Industrialindo', 'nama' => 'BOOSTER REM PS125/HDX', 'part_number' => 'PS125' ],
            [ 'kode' => 'A3', 'supplier' => 'CV Industrialindo', 'nama' => 'MASTER KOPLING BAWAH PS125/HDX', 'part_number' => 'PS125' ],
            [ 'kode' => 'A3', 'supplier' => 'CV Industrialindo', 'nama' => 'BAUT PROP. SHAFT PS125/HDX', 'part_number' => 'PS125' ],
            [ 'kode' => 'A1', 'supplier' => 'CV Industrialindo', 'nama' => 'KACA SPION R/L', 'part_number' => 'PS125' ],
            [ 'kode' => 'A10', 'supplier' => 'CV Industrialindo', 'nama' => 'PER DEPAN ASSY', 'part_number' => 'PS125' ],
            [ 'kode' => 'A10', 'supplier' => 'CV Industrialindo', 'nama' => 'PER BANTU ASSY', 'part_number' => 'PS125' ],
            [ 'kode' => 'A10', 'supplier' => 'CV Industrialindo', 'nama' => 'PER BELAKANG ASSY', 'part_number' => 'PS125' ],
            [ 'kode' => 'A2', 'supplier' => 'CV Industrialindo', 'nama' => 'DINAMO STATER PS125/HDX', 'part_number' => 'PS125' ],
            [ 'kode' => 'A1', 'supplier' => 'CV Industrialindo', 'nama' => 'LAMPU BELAKANG R/L-1', 'part_number' => 'PS125' ],
            [ 'kode' => 'A1', 'supplier' => 'CV Industrialindo', 'nama' => 'LAMPU DEPAN R', 'part_number' => 'PS125' ],
            [ 'kode' => 'A12', 'supplier' => 'CV Industrialindo', 'nama' => 'BAUT RODA DEPAN KANAN', 'part_number' => 'PS125' ],
            [ 'kode' => 'A12', 'supplier' => 'CV Industrialindo', 'nama' => 'AS RODA', 'part_number' => 'PS125' ],
            [ 'kode' => 'A9', 'supplier' => 'CV Industrialindo', 'nama' => 'KAMPAS REM HDX', 'part_number' => 'PS125' ],
            [ 'kode' => 'B3', 'supplier' => 'CV Industrialindo', 'nama' => 'BAN LUAR GRAND V-MILLER', 'part_number' => '7.50-16' ],
            [ 'kode' => 'B3', 'supplier' => 'CV Industrialindo', 'nama' => 'BAN LUAR SUPER V8', 'part_number' => '7.50-16' ],
            [ 'kode' => 'B3', 'supplier' => 'CV Industrialindo', 'nama' => 'BAN DALAM', 'part_number' => '7.50-16' ],
            [ 'kode' => 'B3', 'supplier' => 'CV Industrialindo', 'nama' => 'BAN DALAM', 'part_number' => '11.00-20' ],
            [ 'kode' => 'B12', 'supplier' => 'CV Industrialindo', 'nama' => 'FUEL FILTER 2528C', 'part_number' => '2528C' ],
            [ 'kode' => 'B12', 'supplier' => 'CV Industrialindo', 'nama' => 'FUEL FILTER 2528C', 'part_number' => '2528C' ],
            [ 'kode' => 'B11', 'supplier' => 'CV Industrialindo', 'nama' => 'OIL FILTER 2528C', 'part_number' => '2528C' ],
            [ 'kode' => 'B3', 'supplier' => 'CV Industrialindo', 'nama' => 'Marset', 'part_number' => 'R16' ],
            [ 'kode' => 'B3', 'supplier' => 'CV Industrialindo', 'nama' => 'Marset', 'part_number' => 'R20' ],
            [ 'kode' => 'A12', 'supplier' => 'CV Industrialindo', 'nama' => 'Nut-Washer Belakang Kiri', 'part_number' => 'PS125' ],
            [ 'kode' => 'A10', 'supplier' => 'CV Industrialindo', 'nama' => 'Shockbreaker Depan', 'part_number' => 'PS136' ],
            [ 'kode' => 'A10', 'supplier' => 'CV Industrialindo', 'nama' => 'Shockbreaker Belakang', 'part_number' => 'PS136' ],
            [ 'kode' => 'A12', 'supplier' => 'CV Industrialindo', 'nama' => 'Hub Tromol belakang', 'part_number' => 'PS125' ],
            [ 'kode' => 'A1', 'supplier' => 'CV Industrialindo', 'nama' => 'Lampu Mundur', 'part_number' => 'PS125' ],
            [ 'kode' => 'A6', 'supplier' => 'CV Industrialindo', 'nama' => 'Aki N70+Air', 'part_number' => 'PS125' ],
            [ 'kode' => 'A10', 'supplier' => 'CV Industrialindo', 'nama' => 'Shockbreaker Depan', 'part_number' => 'PS125' ],
            [ 'kode' => 'A10', 'supplier' => 'CV Industrialindo', 'nama' => 'Shockbreaker Belakang', 'part_number' => 'PS125' ],
            [ 'kode' => 'A1', 'supplier' => 'CV Industrialindo', 'nama' => 'Lampu Stop', 'part_number' => 'PS125' ],
            [ 'kode' => 'A11', 'supplier' => 'CV Industrialindo', 'nama' => 'Kuku Bucket', 'part_number' => '205-70-19570' ],
            [ 'kode' => 'A11', 'supplier' => 'CV Industrialindo', 'nama' => 'Pin/Bolt Kuku Bucket', 'part_number' => '092-44-02496' ],
            [ 'kode' => 'A2', 'supplier' => 'CV Industrialindo', 'nama' => 'Water Pump', 'part_number' => '2528C' ],
            [ 'kode' => 'A6', 'supplier' => 'CV Industrialindo', 'nama' => 'Accu 100 A', 'part_number' => 'pc200' ],
            [ 'kode' => 'A12', 'supplier' => 'CV Industrialindo', 'nama' => 'Bolt Shoe', 'part_number' => '20Y-32-31210' ],
            [ 'kode' => 'A12', 'supplier' => 'CV Industrialindo', 'nama' => 'Track Shoe', 'part_number' => 'pc200' ],
            [ 'kode' => 'A11', 'supplier' => 'CV Industrialindo', 'nama' => 'Adaptor', 'part_number' => '20Y-70-14520' ],
            [ 'kode' => 'A11', 'supplier' => 'CV Industrialindo', 'nama' => 'Busing Bucket', 'part_number' => '20Y-70-32410' ],
            [ 'kode' => 'A12', 'supplier' => 'CV Industrialindo', 'nama' => 'Track Roller', 'part_number' => 'd85ess' ],
            [ 'kode' => 'A12', 'supplier' => 'CV Industrialindo', 'nama' => 'Carry Roller', 'part_number' => 'd85ess' ],
            [ 'kode' => 'C1', 'supplier' => 'CV Industrialindo', 'nama' => 'Majun-11' ],
            [ 'kode' => 'C1', 'supplier' => 'CV Industrialindo', 'nama' => 'Kawat Las-11', 'part_number' => 'LB52U 3,2 MM' ],
            [ 'kode' => 'B28', 'supplier' => 'CV Industrialindo', 'nama' => 'Minyak Rem-11', 'part_number' => 'DOT3' ],
            [ 'kode' => 'C1', 'supplier' => 'CV Industrialindo', 'nama' => 'Air Aki Tambah-11' ],
            [ 'kode' => 'C1', 'supplier' => 'CV Industrialindo', 'nama' => 'Locktite Baut-11' ],
            [ 'kode' => 'A12', 'supplier' => 'CV Industrialindo', 'nama' => 'Front Axle-11', 'part_number' => 'PS125' ],
            [ 'kode' => 'A6', 'supplier' => 'CV Industrialindo', 'nama' => 'Aki-11', 'part_number' => 'N100' ],
            [ 'kode' => 'A6', 'supplier' => 'CV Industrialindo', 'nama' => 'Solenoid Pump FIP-11', 'part_number' => '928400617' ],
            [ 'kode' => 'A5', 'supplier' => 'CV Industrialindo', 'nama' => 'Crossjoint-11', 'part_number' => '47,5X135' ],
            [ 'kode' => 'A12', 'supplier' => 'CV Industrialindo', 'nama' => 'Bolt Shoe-11', 'part_number' => 'PC200-8M0' ],
            [ 'kode' => 'A9', 'supplier' => 'CV Industrialindo', 'nama' => 'Adjuster Rem Tengah-11', 'part_number' => 'A4004201318' ],
            [ 'kode' => 'A9', 'supplier' => 'CV Industrialindo', 'nama' => 'Brake Adjuster Rem Tengah-11', 'part_number' => 'A4004230007' ],
            [ 'kode' => 'C1', 'supplier' => 'CV Industrialindo', 'nama' => 'Plat-11', 'part_number' => '3ML' ],
            [ 'kode' => 'C1', 'supplier' => 'CV Industrialindo', 'nama' => 'Plat-11', 'part_number' => '1ML' ],
            [ 'kode' => 'A10', 'supplier' => 'CV Industrialindo', 'nama' => 'Per Depan Assembly-11', 'part_number' => 'PS125' ],
            [ 'kode' => 'A10', 'supplier' => 'CV Industrialindo', 'nama' => 'Per Belakang Assembly-11', 'part_number' => 'PS125' ],
            [ 'kode' => 'A2', 'supplier' => 'CV Industrialindo', 'nama' => 'Engine Trans Mounting-11', 'part_number' => 'PS125' ],
            [ 'kode' => 'B27', 'supplier' => 'CV Industrialindo', 'nama' => 'Grease Chassis-11', 'part_number' => 'Cobra @ 16 Kg' ],
            [ 'kode' => 'B27', 'supplier' => 'CV Industrialindo', 'nama' => 'Grease Bearing-11', 'part_number' => 'TOP1' ],
            [ 'kode' => 'B3', 'supplier' => 'CV Industrialindo', 'nama' => 'Ban Luar Cacing-11', 'part_number' => '7.50-16' ],
            [ 'kode' => 'B3', 'supplier' => 'CV Industrialindo', 'nama' => 'Ban Dalam-11', 'part_number' => '7.50-16' ],
            [ 'kode' => 'B3', 'supplier' => 'CV Industrialindo', 'nama' => 'Flap-11', 'part_number' => 'R16' ],
            [ 'kode' => 'A12', 'supplier' => 'CV Industrialindo', 'nama' => 'Spring Adjuster-11', 'part_number' => 'PC200-8M0' ],
            [ 'kode' => 'A6', 'supplier' => 'Elektronik', 'nama' => 'Repair Alternator' ],
            [ 'kode' => 'A6', 'supplier' => 'Elektronik', 'nama' => 'Repair Alternator' ],
            [ 'kode' => 'A7', 'supplier' => 'Mattoangin', 'nama' => 'Oring box' ],
            [ 'kode' => 'A12', 'supplier' => 'Mattoangin', 'nama' => 'Radial Shaft seal' ],
            [ 'kode' => 'B3', 'supplier' => 'Mattoangin', 'nama' => 'Ban dalam 23.1-26' ],
            [ 'kode' => 'A6', 'supplier' => 'Mattoangin', 'nama' => 'Regulator Switch Axor' ],
            [ 'kode' => 'A7', 'supplier' => 'Mattoangin', 'nama' => 'Oring box' ],
            [ 'kode' => 'A12', 'supplier' => 'Mattoangin', 'nama' => 'Radial Shaft seal' ],
            [ 'kode' => 'B3', 'supplier' => 'Mattoangin', 'nama' => 'Ban dalam 23.1-26' ],
            [ 'kode' => 'A6', 'supplier' => 'Mattoangin', 'nama' => 'Regulator Switch Axor' ],
            [ 'kode' => 'A7', 'supplier' => 'Mattoangin', 'nama' => 'Hose 1/4 230cm' ],
            [ 'kode' => 'A7', 'supplier' => 'Mattoangin', 'nama' => 'Hose 1/4 60cm' ],
            [ 'kode' => 'A5', 'supplier' => 'Mattoangin', 'nama' => 'Rotary Shaft Seal Gardan Axor' ],
            [ 'kode' => 'A6', 'supplier' => 'Mattoangin', 'nama' => 'Selenoid Valve dll' ],
            [ 'kode' => 'A7', 'supplier' => 'Mattoangin', 'nama' => 'Hose 1/4 170cm' ],
            [ 'kode' => 'B11', 'supplier' => 'PT Adhie Usaha Mandiri', 'nama' => 'Engine Oil Filter-12', 'part_number' => '6736-51-5142' ],
            [ 'kode' => 'B12', 'supplier' => 'PT Adhie Usaha Mandiri', 'nama' => 'Fuel Pre Filter-12', 'part_number' => '600-319-5610' ],
            [ 'kode' => 'B12', 'supplier' => 'PT Adhie Usaha Mandiri', 'nama' => 'Fuel Filter-12', 'part_number' => '600-319-3750' ],
            [ 'kode' => 'B13', 'supplier' => 'PT Adhie Usaha Mandiri', 'nama' => 'Air Cleaner-12', 'part_number' => 'P532966' ],
            [ 'kode' => 'B11', 'supplier' => 'PT Adhie Usaha Mandiri', 'nama' => 'Engine Oil Filter-12', 'part_number' => 'P502008' ],
            [ 'kode' => 'B12', 'supplier' => 'PT Adhie Usaha Mandiri', 'nama' => 'Fuel Filter-12', 'part_number' => 'P552561' ],
            [ 'kode' => 'B13', 'supplier' => 'PT Adhie Usaha Mandiri', 'nama' => 'Air Cleaner-12', 'part_number' => 'A1088' ],
            [ 'kode' => 'B11', 'supplier' => 'PT. Gala Jaya Mandiri', 'nama' => 'Oil Filter', 'part_number' => '6736-51-5142/P55-8615' ],
            [ 'kode' => 'B12', 'supplier' => 'PT. Gala Jaya Mandiri', 'nama' => 'Fuel Pre Filter', 'part_number' => '600-319-5610' ],
            [ 'kode' => 'B12', 'supplier' => 'PT. Gala Jaya Mandiri', 'nama' => 'Fuel Filter', 'part_number' => '600-319-3750/186-30180' ],
            [ 'kode' => 'B13', 'supplier' => 'PT. Gala Jaya Mandiri', 'nama' => 'Fuel Tank Brea ther', 'part_number' => '421-60-35170' ],
            [ 'kode' => 'B14', 'supplier' => 'PT. Gala Jaya Mandiri', 'nama' => 'Hidrolik Filter', 'part_number' => '207-60-71183/186-30180' ],
            [ 'kode' => 'A2', 'supplier' => 'PT. Gala Jaya Mandiri', 'nama' => 'Injector Assembly', 'part_number' => '6754-11-3011/0 445 120 059' ],
            [ 'kode' => 'A1', 'supplier' => 'PT. Gala Jaya Mandiri', 'nama' => 'Monitor', 'part_number' => '7835-34-1002' ],
            [ 'kode' => 'A4', 'supplier' => 'PT. Gala Jaya Mandiri', 'nama' => 'Pin Foot Boom', 'part_number' => '206-70-55160' ],
            [ 'kode' => 'A4', 'supplier' => 'PT. Gala Jaya Mandiri', 'nama' => 'Spacer T = 2,0 mm', 'part_number' => '22U-70-31360' ],
            [ 'kode' => 'A4', 'supplier' => 'PT. Gala Jaya Mandiri', 'nama' => 'Bearing', 'part_number' => '20Y-26-22440' ],
            [ 'kode' => 'A4', 'supplier' => 'PT. Gala Jaya Mandiri', 'nama' => 'Shaft', 'part_number' => '206-26-69112' ],
            [ 'kode' => 'A4', 'supplier' => 'PT. Gala Jaya Mandiri', 'nama' => 'Cover', 'part_number' => '20Y-26-22191' ],
            [ 'kode' => 'A11', 'supplier' => 'PT. Gala Jaya Mandiri', 'nama' => 'Bucket', 'part_number' => '20Y-920-1110' ],
            [ 'kode' => 'A11', 'supplier' => 'PT. Gala Jaya Mandiri', 'nama' => 'Tooth Std', 'part_number' => '205-70-19570' ],
            [ 'kode' => 'A11', 'supplier' => 'PT. Gala Jaya Mandiri', 'nama' => 'Bolt Tooth', 'part_number' => 'Baja 12,9' ],
            [ 'kode' => 'A11', 'supplier' => 'PT. Gala Jaya Mandiri', 'nama' => 'Spacer', 'part_number' => '205-70-74381' ],
            [ 'kode' => 'A11', 'supplier' => 'PT. Gala Jaya Mandiri', 'nama' => 'Pin', 'part_number' => '20Y-70-73270' ],
            [ 'kode' => 'A12', 'supplier' => 'PT. Gala Jaya Mandiri', 'nama' => 'Sprocket', 'part_number' => '20Y-27-11582' ],
            [ 'kode' => 'A12', 'supplier' => 'PT. Gala Jaya Mandiri', 'nama' => 'Bolt Sprocket', 'part_number' => '20Y-27-11561' ],
            [ 'kode' => 'A12', 'supplier' => 'PT. Gala Jaya Mandiri', 'nama' => 'Track Roller', 'part_number' => '20Y-30-07300' ],
            [ 'kode' => 'A12', 'supplier' => 'PT. Gala Jaya Mandiri', 'nama' => 'Bolt Track shoe', 'part_number' => '20Y-30-11340' ],
            [ 'kode' => 'A12', 'supplier' => 'PT. Gala Jaya Mandiri', 'nama' => 'Carry Roller', 'part_number' => '20Y-30-00670' ],
            [ 'kode' => 'A12', 'supplier' => 'PT. Gala Jaya Mandiri', 'nama' => 'Bolt Carrie Roller', 'part_number' => '01010-81680' ],
            [ 'kode' => 'B11', 'supplier' => 'PT. Gala Jaya Mandiri', 'nama' => 'Oil Filter', 'part_number' => '600-211-1231/P55-1670' ],
            [ 'kode' => 'B12', 'supplier' => 'PT. Gala Jaya Mandiri', 'nama' => 'Fuel Filter', 'part_number' => '600-311-8293/186-2029' ],
            [ 'kode' => 'B13', 'supplier' => 'PT. Gala Jaya Mandiri', 'nama' => 'Air Cleaner', 'part_number' => '6125-81-7032/P18-1046' ],
            [ 'kode' => 'B14', 'supplier' => 'PT. Gala Jaya Mandiri', 'nama' => 'Hyd rolic Filter', 'part_number' => '07063-01100/P50-7830' ],
            [ 'kode' => 'A3', 'supplier' => 'PT. Gala Jaya Mandiri', 'nama' => 'Joint Assy', 'part_number' => '14X-11-11100' ],
            [ 'kode' => 'A2', 'supplier' => 'PT. Gala Jaya Mandiri', 'nama' => 'Pug/Bolt Oil Pan', 'part_number' => '6150-23-5620' ],
            [ 'kode' => 'A6', 'supplier' => 'PT. Gala Jaya Mandiri', 'nama' => 'Alternator', 'part_number' => '6N-9294' ],
            [ 'kode' => 'A2', 'supplier' => 'PT. Gala Jaya Mandiri', 'nama' => 'Connector', 'part_number' => '6151-11-8650' ],
            [ 'kode' => 'A2', 'supplier' => 'PT. Gala Jaya Mandiri', 'nama' => 'Bolt', 'part_number' => '01010-81225' ],
            [ 'kode' => 'B11', 'supplier' => 'PT. Gala Jaya Mandiri', 'nama' => 'Oil Filter', 'part_number' => '4032-64005-0/P55-0596' ],
            [ 'kode' => 'B12', 'supplier' => 'PT. Gala Jaya Mandiri', 'nama' => 'Fuel Filter', 'part_number' => '4032-09002-0/186-2022' ],
            [ 'kode' => 'B12', 'supplier' => 'PT. Gala Jaya Mandiri', 'nama' => 'Element W Sparator', 'part_number' => '4421-39001-0/186-2007' ],
            [ 'kode' => 'B14', 'supplier' => 'PT. Gala Jaya Mandiri', 'nama' => 'Catride Hvdraulic', 'part_number' => '4211-41001-1/P17-7047' ],
            [ 'kode' => 'B22', 'supplier' => 'PT. Gala Jaya Mandiri', 'nama' => 'Oli Hidrolik' ],
            [ 'kode' => 'C1', 'supplier' => 'PT. Gala Jaya Mandiri', 'nama' => 'Kawat Las', 'part_number' => 'LB52U 3,2 MM' ],
            [ 'kode' => 'C1', 'supplier' => 'PT. Gala Jaya Mandiri', 'nama' => 'Air Aki Tambah' ],
            [ 'kode' => 'C1', 'supplier' => 'PT. Gala Jaya Mandiri', 'nama' => 'Majun' ],
            [ 'kode' => 'A2', 'supplier' => 'PT LTA Diesel Engine Service', 'nama' => 'Injector Assy D6G2-12', 'part_number' => 'D6G2XL' ],
            [ 'kode' => 'A7', 'supplier' => 'PT Sacon Indonesia', 'nama' => 'Motor Vibro', 'part_number' => '4216-48000-0' ],
            [ 'kode' => 'A7', 'supplier' => 'PT Sacon Indonesia', 'nama' => 'Pump Vibro', 'part_number' => '4216-44002-0' ],
            [ 'kode' => 'A7', 'supplier' => 'PT Sacon Indonesia', 'nama' => 'Hose', 'part_number' => '2301-32314-1' ],
            [ 'kode' => 'B23', 'supplier' => 'PT Sefas Keliantama', 'nama' => 'Oli Transmisi', 'part_number' => 'Spirax S2 G 90' ],
            [ 'kode' => 'B21', 'supplier' => 'PT Sefas Keliantama', 'nama' => 'Oli Mesin', 'part_number' => 'Rimula R4 15W-40' ],
            [ 'kode' => 'B26', 'supplier' => 'PT Sefas Keliantama', 'nama' => 'Oli Gardan', 'part_number' => 'Spirax S2 A140' ],
            [ 'kode' => 'B21', 'supplier' => 'PT Sefas Keliantama', 'nama' => 'Oli Rimula 15W40-12', 'part_number' => '15W-40' ],
            [ 'kode' => 'B22', 'supplier' => 'PT Sefas Keliantama', 'nama' => 'Oli Tellus 68-12', 'part_number' => 'Tellus 68' ],
            [ 'kode' => 'B12', 'supplier' => 'PT. Gala Jaya Mandiri', 'nama' => 'Fuel Filter', 'part_number' => '600-311-8321' ],
            [ 'kode' => 'B12', 'supplier' => 'PT. Gala Jaya Mandiri', 'nama' => 'Elemet Assy', 'part_number' => '600-181-6740' ],
            [ 'kode' => 'A2', 'supplier' => 'PT. Gala Jaya Mandiri', 'nama' => 'V-Belt Set (Cooling Fan)', 'part_number' => '04121-21754' ],
            [ 'kode' => 'B16', 'supplier' => 'PT. Gala Jaya Mandiri', 'nama' => 'Cartridge (Corrosion Resistor)', 'part_number' => '600-411-1191' ],
            [ 'kode' => 'B12', 'supplier' => 'PT. Gala Jaya Mandiri', 'nama' => 'Filter Solar', 'part_number' => '458758' ],
            [ 'kode' => 'B14', 'supplier' => 'PT. Gala Jaya Mandiri', 'nama' => 'Element Hydrolic', 'part_number' => '4211-41001-0' ],
            [ 'kode' => 'C1', 'supplier' => 'Sumatra', 'nama' => 'LPG dll' ],
            [ 'kode' => 'C1', 'supplier' => 'Sumatra', 'nama' => 'Oksigen' ],
            [ 'kode' => 'A12', 'supplier' => 'Sumber Coklat', 'nama' => 'Lahar 33210 dll' ],
            [ 'kode' => 'A12', 'supplier' => 'Sumber Coklat', 'nama' => 'Lahar 33210 dll' ],
            [ 'kode' => 'A12', 'supplier' => 'Sumber Coklat', 'nama' => 'Lahar 32214' ],
            [ 'kode' => 'C1', 'supplier' => 'Sumber Coklat', 'nama' => 'Kepala Aki' ],
            [ 'kode' => 'C1', 'supplier' => 'Sumber Coklat', 'nama' => 'Pompa Grease dll' ],
            [ 'kode' => 'A12', 'supplier' => 'Sumber Coklat', 'nama' => 'Lahar 33210 dll' ],
            [ 'kode' => 'A12', 'supplier' => 'Sumber Coklat', 'nama' => 'Lahar 33210 dll' ],
            [ 'kode' => 'A12', 'supplier' => 'Sumber Coklat', 'nama' => 'Lahar 32214' ],
            [ 'kode' => 'C1', 'supplier' => 'Sumber Coklat', 'nama' => 'Kepala Aki' ],
            [ 'kode' => 'C1', 'supplier' => 'Sumber Coklat', 'nama' => 'Pompa Grease dll' ],
            [ 'kode' => 'B22', 'supplier' => 'Sumber Coklat', 'nama' => 'Oli SAE10' ],
            [ 'kode' => 'A6', 'supplier' => 'Sumber Coklat', 'nama' => 'Lampu Mundur HD dll' ],
            [ 'kode' => 'A12', 'supplier' => 'Sumber Coklat', 'nama' => 'Bearing 33210' ],
            [ 'kode' => 'C1', 'supplier' => 'Sumber Coklat Teknik', 'nama' => 'Timah dll' ],
            [ 'kode' => 'C1', 'supplier' => 'Sumber Coklat Teknik', 'nama' => 'Colokan Las' ],
            [ 'kode' => 'C1', 'supplier' => 'Sumber Coklat Teknik', 'nama' => 'Timah dll' ],
            [ 'kode' => 'C1', 'supplier' => 'Sumber Coklat Teknik', 'nama' => 'Colokan Las' ],
            [ 'kode' => 'C1', 'supplier' => 'Sumber Coklat Teknik', 'nama' => 'Holder Las dll' ],
            [ 'kode' => 'C1', 'supplier' => 'Sumber Coklat Teknik', 'nama' => 'Mur 24 dll' ],
        ];

        foreach ( $data as $item )
        {
            // Find kategori
            $kategori = KategoriSparepart::where ( 'kode', $item[ 'kode' ] )->first ();
            if ( ! $kategori ) continue;

            // Find or create supplier
            $supplier = MasterDataSupplier::where ( 'nama', $item[ 'supplier' ] )->first ();
            if ( ! $supplier ) continue;

            // Use firstOrCreate to avoid duplicate entries
            $sparepart = MasterDataSparepart::firstOrCreate (
                [ 
                    'nama'        => $item[ 'nama' ],
                    'part_number' => $item[ 'part_number' ] ?? '-'
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
        $proyek = Proyek::where ( 'nama', 'BENDUNGAN BUDONG - BUDONG' )->first ();
        if ( ! $proyek )
        {
            console ( "Project 'BENDUNGAN BUDONG - BUDONG' not found!" );
            return;
        }
        console ( "Found project: " . $proyek->nama . " (ID: " . $proyek->id . ")" );

        // Define equipment data with all equipment codes
        $alatCodes = [ 
            'BL 010-17',
            'BL 028-20',
            'CE 075-20',
            'CE 080-20',
            'CE 081-20',
            'CE 120-20',
            'CE 123-20',
            'CE 135-20',
            'DT 087-8',
            'DT 095-8',
            'DT 104-8',
            'DT 105-8',
            'HD 081-15',
            'HD 083-15',
            'HD 091-15',
            'HD 096-15',
            'HD 097-15',
            'HD 099-15',
            'HD 102-15',
            'HD 105-15',
            'HD 130-15',
            'HD 134-15',
            'HD 143-15',
            'HD 162-15',
            'VR 013-10'
        ];

        console ( "Looking for " . count ( $alatCodes ) . " equipment records" );

        // Find existing equipment
        $existingAlats = MasterDataAlat::whereIn ( 'kode_alat', $alatCodes )->get ();
        console ( "Found " . $existingAlats->count () . " existing equipment records" );

        // Log all found equipment
        foreach ( $existingAlats as $alat )
        {
            console ( "Found equipment: " . $alat->kode_alat . " (ID: " . $alat->id . ")" );
        }

        // Find missing equipment codes
        $existingCodes = $existingAlats->pluck ( 'kode_alat' )->toArray ();
        $missingCodes  = array_diff ( $alatCodes, $existingCodes );

        if ( count ( $missingCodes ) > 0 )
        {
            console ( "Missing equipment codes: " . implode ( ', ', $missingCodes ) );
        }
        else
        {
            console ( "All equipment codes found in database" );
        }

        // Process each equipment
        foreach ( $existingAlats as $alat )
        {
            // Check if the equipment is already linked to this project
            $existing = AlatProyek::where ( 'id_proyek', $proyek->id )
                ->where ( 'id_master_data_alat', $alat->id )
                ->whereNull ( 'removed_at' )
                ->first ();

            if ( $existing )
            {
                console ( "Equipment " . $alat->kode_alat . " already linked to this project" );
                continue;
            }

            console ( "Linking equipment " . $alat->kode_alat . " to project" );

            try
            {
                // Link equipment to project
                $alatProyek = AlatProyek::create ( [ 
                    'id_proyek'           => $proyek->id,
                    'id_master_data_alat' => $alat->id,
                    'assigned_at'         => now (),
                    'removed_at'          => null
                ] );

                console ( "Successfully created AlatProyek record with ID: " . $alatProyek->id );

                // Update the current project for the equipment
                $alat->update ( [ 
                    'id_proyek_current' => $proyek->id
                ] );

                console ( "Updated equipment's current project" );
            }
            catch ( \Exception $e )
            {
                console ( "ERROR linking equipment: " . $e->getMessage () );
            }
        }

        console ( "Equipment linking process completed" );
    }
}
