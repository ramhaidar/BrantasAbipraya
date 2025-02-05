<?php

namespace App\Imports;

use App\Models\User;
use App\Models\CarModel;
use App\Models\CarColor;
use App\Models\Dealer;
use App\Models\CarType;
use App\Models\ChapterTag;
use App\Models\Regency;
use Maatwebsite\Excel\Concerns\ToModel;

class UserImport implements ToModel
{
    protected $existingData = [];

    public function __construct ()
    {
        $dataUsers = User::all ( [ 'id_member', 'license_plate', 'vin' ] );
        foreach ( $dataUsers as $user )
        {
            $this->existingData[] = [ 
                'id_member'     => $user->id_member,
                'license_plate' => $user->license_plate,
                'vin'           => $user->vin,
            ];
        }
    }

    public function formatPhone ( $phone )
    {
        $data  = preg_replace ( '/[^0-9]/', '', $phone );
        $index = strpos ( $data, '8' );
        if ( $index != '' )
        {
            return substr ( $data, $index );
        }
        else
        {
            return null;
        }
    }

    public function getDaerah ( $string )
    {
        $arrRegency = explode ( ' ', $string );
        $i          = 1;
        $daerah     = Regency::where ( 'name', 'ilike', '%' . strtoupper ( $arrRegency[ 0 ] ) . '%' )->first ();
        while ( $i < count ( $arrRegency ) && ! $daerah )
        {
            $daerah = Regency::where ( 'name', 'ilike', '%' . strtoupper ( $arrRegency[ $i ] ) . '%' )->first ();
            $i++;
        }
        return optional ( $daerah )->id;
    }

    private function getIdMember ( int $id )
    {
        $idMember = $id + 1;
        while ( strpos ( $idMember, '4' ) !== false || fmod ( $idMember, 100 ) == 13 )
        {
            $idMember++;
        }
        return $idMember;
    }
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model ( array $row )
    {
        if ( ! ( $row[ 0 ] == 'ID Member' && $row[ 1 ] == 'Name' && $row[ 2 ] == 'Sex' && $row[ 3 ] == 'Domicile' && $row[ 4 ] == 'Phone' && $row[ 5 ] == 'License Plate' && $row[ 6 ] == 'Colour' && $row[ 7 ] == 'Nick Name' && $row[ 8 ] == 'Range Type' && $row[ 9 ] == 'Car Model' && $row[ 10 ] == 'Dealer' && $row[ 11 ] == 'VIN' && $row[ 12 ] == 'Emergency Contact' && $row[ 13 ] == 'Blood Type' && $row[ 14 ] == 'T-Shirt Size' && $row[ 15 ] == 'Info WEVI' && $row[ 16 ] == 'Ketua Chapter' ) )
        {
            $id_member = intval ( ltrim ( $row[ 0 ], '0' ) );
            if ( in_array ( $id_member, array_column ( $this->existingData, 'id_member' ) ) )
            {
                $id_member = $this->getIdMember ( User::max ( 'id_member' ) );
            }

            $phone = $this->formatPhone ( $row[ 4 ] );
            $plate = strtoupper ( $row[ 5 ] ) == 'SEMENTARA' || $row[ 5 ] == '' || in_array ( str_replace ( ' ', '', $row[ 5 ] ), array_column ( $this->existingData, 'license_plate' ) ) ? 'plate-' . $id_member : str_replace ( ' ', '', $row[ 5 ] );
            $vin   = strtoupper ( $row[ 11 ] ) == 'SEMENTARA' || ! $row[ 11 ] || in_array ( $row[ 11 ], array_column ( $this->existingData, 'vin' ) ) ? 'vin' . $id_member : $row[ 11 ];

            $this->existingData[] = [ 
                'id_member'     => $id_member,
                'license_plate' => $plate,
                'vin'           => $vin,
            ];

            $daerah = Regency::where ( 'name', strtoupper ( $row[ 3 ] ) )->first ();
            $model  = CarModel::where ( 'name', $row[ 9 ] )->first ();
            return new User( [ 
                'id_member'         => $id_member,
                'role'              => 'Member',
                'status_member'     => 1,
                'name'              => $row[ 1 ],
                'sex'               => $row[ 2 ],
                'domicile_id'       => $daerah ? optional ( $daerah )->id : $this->getDaerah ( $row[ 3 ] ),
                'phone'             => $phone,
                'license_plate'     => $plate,
                'car_color_id'      => $model ? optional ( CarColor::where ( 'car_model_id', $model->id )->where ( 'name', $row[ 6 ] )->first () )->id : null,
                'nickname'          => ( $row[ 7 ] ? $row[ 7 ] : '' ),
                'car_type_id'       => $model ? optional ( CarType::where ( 'car_model_id', $model->id )->where ( 'name', $row[ 8 ] )->first () )->id : null,
                'car_model_id'      => optional ( $model )->id,
                'dealer_id'         => optional ( Dealer::where ( 'name', $row[ 10 ] )->first () )->id,
                'vin'               => $vin,
                'sim_a'             => '',
                'emergency_contact' => $this->formatPhone ( $row[ 12 ] ),
                'blood_type'        => ( $row[ 13 ] == 'A' || $row[ 13 ] == 'A+' || $row[ 13 ] == 'A-' || $row[ 13 ] == 'AB' || $row[ 13 ] == 'AB+' || $row[ 13 ] == 'AB-' || $row[ 13 ] == 'B' || $row[ 13 ] == 'B+' || $row[ 13 ] == 'B-' || $row[ 13 ] == 'O' || $row[ 13 ] == 'O+' || $row[ 13 ] == 'O-' ) ? $row[ 13 ] : '',
                't_shirt_size'      => ( $row[ 14 ] == 'XXL' ? '2XL' : ( $row[ 14 ] == 'XXXL' ? '3XL' : ( $row[ 14 ] == 'XXXXL' ? '4XL' : ( $row[ 14 ] == 'S' || $row[ 14 ] == 'M' || $row[ 14 ] == 'L' || $row[ 14 ] == 'XL' || $row[ 14 ] == '2XL' || $row[ 14 ] == '3XL' || $row[ 14 ] == '4XL' || $row[ 14 ] == '5XL' || $row[ 14 ] == '6XL' ? $row[ 14 ] : null ) ) ) ),
                'info_wevi'         => $row[ 15 ],
                'ketua_chapter_id'  => $daerah ? optional ( ChapterTag::where ( 'tag_id', $daerah->id )->first () )->ketua_chapter_id : null,
                'password'          => 'pass' . $id_member
            ] );
        }
    }
}
