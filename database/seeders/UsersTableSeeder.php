<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    public function run () : void
    {
        $roles              = [ 'Pegawai', 'Admin', 'Boss' ];
        $users              = [ 'pegawai', 'admin', 'boss' ];
        $passwords          = [ 'pegawai123', 'admin123', 'boss123' ];
        $defaultProfilePath = '\UserDefault.png';

        foreach ( $roles as $index => $role )
        {
            User::factory ()->create ( [ 
                'name'         => $role,
                'username'     => $users[ $index ],
                'role'         => $role,
                'password'     => Hash::make ( $passwords[ $index ] ),
                'path_profile' => $defaultProfilePath,
            ] );
        }

        User::factory ()->create ( [ 
            'name'         => 'John Doe Smith William',
            'username'     => 'johndoe',
            'role'         => 'Pegawai',
            'password'     => Hash::make ( 'john1234' ),
            'path_profile' => $defaultProfilePath,
        ] );
    }
}