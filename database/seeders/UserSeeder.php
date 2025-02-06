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

        $emails = [ 
            'superadmin@brantas-abipraya.co.id',
            'svp@brantas-abipraya.co.id',
            'vp@brantas-abipraya.co.id',
            'admin.divisi@brantas-abipraya.co.id',
            'koordinator@brantas-abipraya.co.id'
        ];

        $names = [ 
            'Super Administrator',
            'Senior Vice President',
            'Vice President',
            'Admin Divisi',
            'Koordinator Proyek'
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
                'name'         => $names[ $index ],
                'username'     => $users[ $index ],
                'email'        => $emails[ $index ],
                'role'         => $role,
                'password'     => Hash::make ( $passwords[ $index ] ),
                'path_profile' => $defaultProfilePath,
            ] );
        }
    }
}