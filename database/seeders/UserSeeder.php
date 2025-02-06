<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run () : void
    {
        $roles = [ 
            'superadmin',
            'svp',
            'vp',
            'admin_divisi',
            'koordinator_proyek'
        ];

        $users = [ 
            'superadmin',
            'svp',
            'vp',
            'admin_divisi',
            'koordinator'
        ];

        $passwords = [ 
            'superadmin123',
            'svp123',
            'vp123',
            'admin123',
            'koordinator123'
        ];

        $defaultProfilePath = '/UserDefault.png';

        foreach ( $roles as $index => $role )
        {
            User::factory ()->create ( [ 
                'name'         => ucfirst ( $role ),
                'username'     => $users[ $index ],
                'role'         => $role,
                'password'     => Hash::make ( $passwords[ $index ] ),
                'path_profile' => $defaultProfilePath,
            ] );
        }
    }
}