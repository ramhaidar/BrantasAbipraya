<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;

class UserExportActive implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $users = User::where('status_member', 1)->get();
        $data = [];
        $data[] = [
            'id_member' => 'ID Member',
            'name' => 'Name',
            'sex' => 'Sex',
            'domicile' => 'Domicile',
            'phone' => 'Phone',
            'license_plate' => 'License Plate',
            'car_color' => 'Color',
            'nickname' => 'Nickname',
            'car_type' => 'Range Type',
            'car_model' => 'Car Model',
            'dealer' => 'Dealer',
            'vin' => 'VIN',
            'emergency_contact' => 'Emergency Plate',
            'blood_type' => 'Blood Type',
            't_shirt_size' => 'T-Shirt Size',
            'info_wevi' => 'Info WEVI',
            'ketua_chapter' => 'Ketua Chapter'
        ];
        foreach ($users as $user) {
            $data[] = [
                'id_member' => $user->id_member,
                'name' => $user->name,
                'sex' => $user->sex,
                'domicile' => optional($user->domicile)->name,
                'phone' => (strpos($user->phone, 'phone') !== false ? '' : '+62'.$user->phone),
                'license_plate' => (strpos($user->license_plate, 'plate') !== false ? '' : $user->license_plate),
                'car_color' => optional($user->car_color)->name,
                'nickname' => $user->nickname,
                'car_type' => optional($user->car_type)->name,
                'car_model' => optional($user->car_model)->name,
                'dealer' => optional($user->dealer)->name,
                'vin' => (strpos($user->vin, 'vin') !== false ? '' : $user->vin),
                'emergency_contact' => $user->emergency_contact,
                'blood_type' => $user->blood_type,
                't_shirt_size' => $user->t_shirt_size,
                'info_wevi' => $user->info_wevi,
                'ketua_chapter' => optional($user->ketua_chapter)->name
            ];
        }

        return new Collection($data);
    }
}
