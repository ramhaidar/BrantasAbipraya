<?php

namespace Database\Factories;

use App\Models\MasterDataAlat;
use App\Models\KategoriSparepart;
use App\Models\MasterDataSparepart;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MasterDataAlat>
 */
class MasterDataSparepartFactory extends Factory
{
    protected $model = MasterDataSparepart::class;

    private static $nama = [ "Oli Hydraulic Pertamina", "Oli Mesin Pertamina", "Oli Transmisi Pertamina", "Oli Final Drive Pertamina", "Oli Gardan", "Ban Luar Cakar", "Ban Dalam", "Marset", "Flap", "Seal kit Adjuster (LH/RH)", "Track Roller Assembly", "Tooth Standart", "Tooth Runcing", "Pin Tooth", "Bolt Idler (RH/LH)", "Bolt Sproket (RH/LH)", "Idler Assy (RH/LH)", "Sprocket", "Bucket", "Carrie Roller", "Bolt", "Recoil Spring Assembly (LH/RH)", "Nepel Grease", "Nepel Adjuster", "Bolam lampu 24 V" ];
    private static $part_number = [ "White", "20Y-04-J1130", "6742 01 4540", "P502426 DONALDSON", "2A5-62-11131", "20Y-62-51222", "9364-0984", "702-21-57400", "6754-61-6212", "260-7996", "260-7988", "376-6342", "322-3155", "147-2645", "PC 200-8MO", "A400 4310115", "A400 4770008", "A400 4770007", "J86-20220", "SAKAI", "CT 70B", "Tekiro", "220-9094", "38-8489", "360-8960", "093-7521", "6I-2504", "6I-2503", "462-1171", "439-5037", "337-5270", "IR-1808", "126-1818", "22U-70-21191", "205-70-73180", "21K-70-12161", "20Y-70-14520", "09244-02496", "H11", "H7", "H4", "H3", "14Y-50-11323", "A400 3240 214", "A400 3210 411", "A400 3210 312", "A400 3210 214", "A400 3310 125", "A400 3512 425", "A006 9908 504", "A149901 001", "A000000005401", "Super HD", "Canter", "707-56-70540", "6732-81-3531", "ND028530-1100", "A400-1500-101", "A400-4700-035", "A400-4212--130", "A400-3230-668", "A400-3230--468", "A400-3230--568", "A400-3241--213", "A400-3301-703", "A400-3571-901", "A400-3572-001", "Jumbo", "isi 12 ltr Jumbo", "isi 5 ltr Vegacool", "A4003304820", "ND028200-1590", "20Y-032-2110", "23414-E0010", "J86-20561", "J86-21314", "6736-51-5142", "IR-0762", "FC 1005", "C-1007", "Canter FC 1002", "Spirax S4 CX 30", "Rored EPA 90", "Rored HDA 140", "2A5-62-12791", "A400 471 0430", "J8621160", "SFO8551", "EF10080", "AUD-446 1895-KWK", "F11140", "C1153", "P532966", "P533781", "J8638670", "A400 2401 118", "A400 4110 711", "A400 4111 211", "A400 5011 082", "A400 4010 118", "A400 4010 972", "A400 4010 2071", "A400 4201 338", "A400 4201 238", "A400 4200 038", "A005 2032 675", "A400 9811 818", "A400 9970 745", "A008997 77 46", "Turalik 52", "Meditran SC SAE 15W-40", "Transilk HD 30", "GT Lug Pro 7.50-16.14PR", "GT 7.50-16", "GT 16 L", "GT Super Grip 11.0020 18PR", "GT 11.00-20", "GT 20R", "GT Miller 11.00-20 16PR", "GT 175-65 R13 Maxmiler Pro", "GT Champiro 205/65 R16 EC 300+", "GT 265/65 R17-08 Plus", "ADJ-PC200", "20Y-30-07300/00010", "205-70-19570", "01010-61655", "20Y-27-11561", "20Y-30-00640/00320", "20Y-27-11582", "20Y-920-J110/20Y-70-X4101J", "20Y-30-00670/00481", "01010-61660", "07020-00000", "07959-20001" ];
    private static $merk = [ "Bosch", "Denso", "Delphi", "Mahle", "Cummins", "NGK", "Valeo", "Federal-Mogul", "Hitachi", "Nippon Denso", "Brembo", "TRW", "Ferodo", "Akebono", "ATE", "Hawk Performance", "EBC Brakes", "AP Racing", "Raybestos", "Monroe", "KYB", "Bilstein", "Sachs", "Moog", "Gabriel", "Lemforder", "SKF", "Mann+Hummel", "Fram", "K&N", "WIX Filters", "Donaldson", "Purolator", "ACDelco", "Hengst", "Exide", "Yuasa", "Varta", "Optima", "Odyssey", "Interstate", "DieHard", "Amaron", "Philips", "Osram", "Hella", "PIAA", "GE Lighting", "Stanley Electric", "Koito", "Magneti Marelli", "ZF", "Aisin", "Getrag", "BorgWarner", "Allison Transmission", "LuK", "Schaeffler", "Jatco", "Exedy", "Behr", "Nissens", "Modine", "Tata Green", "Gates", "Mishimoto", "Siemens", "Mitsubishi Electric", "Magnaflow", "Bosal", "Walker", "AP Exhaust", "Flowmaster", "Ansa", "Eberspächer", "Borla", "Akrapovic", "Remus" ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition () : array
    {
        return [ 
            'nama'                  => $this->faker->randomElement ( self::$nama ),
            'part_number'           => $this->faker->randomElement ( self::$part_number ),
            'merk'                  => $this->faker->randomElement ( self::$merk ),
            'id_kategori_sparepart' => KategoriSparepart::query ()->inRandomOrder ()->value ( 'id' ),
        ];
    }
}
