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
            'koordinator_proyek',
            'koordinator_proyek', // Koor Satu
            'koordinator_proyek', // Koor Dua
            'koordinator_proyek'  // Koor Tiga
        ];

        $emails = [ 
            'superadmin@brantas-abipraya.co.id',
            'svp@brantas-abipraya.co.id',
            'vp@brantas-abipraya.co.id',
            'admin.divisi@brantas-abipraya.co.id',
            'koordinator@brantas-abipraya.co.id',
            'koorsatu@email.com',  // Koor Satu
            'koordua@email.com',   // Koor Dua
            'koortiga@email.com'   // Koor Tiga
        ];

        $names = [ 
            'Super Administrator',
            'Senior Vice President',
            'Vice President',
            'Admin Divisi',
            'Koordinator Proyek',
            'Nama Koor Satu',  // Koor Satu
            'Nama Koor Dua',   // Koor Dua
            'Nama Koor Tiga'   // Koor Tiga
        ];

        $users = [ 
            'superadmin',
            'svp',
            'vp',
            'admin_divisi',
            'koordinator',
            'koorsatu',  // Koor Satu
            'koordua',   // Koor Dua
            'koortiga'   // Koor Tiga
        ];

        $passwords = [ 
            'superadmin123',
            'svp123',
            'vp123',
            'admin123',
            'koordinator123',
            'dpp12345',  // Koor Satu
            'dpp12345',  // Koor Dua
            'dpp12345'   // Koor Tiga
        ];

        $defaultProfilePath = '/UserDefault.png';

        for ( $i = 0; $i < count ( $users ); $i++ )
        {
            $attributes = User::factory ()->withCredentials (
                $users[ $i ],
                $names[ $i ],
                $emails[ $i ],
                $roles[ $i ],
                $passwords[ $i ]
            )->raw ();

            User::firstOrCreate (
                [ 'email' => $emails[ $i ] ], // unique identifier
                $attributes
            );
        }

        // Create random users only if less than 15 additional users exist
        $additionalUsersCount = 15 - User::where ( 'email', 'not like', '%brantas-abipraya.co.id' )->count ();
        if ( $additionalUsersCount > 0 )
        {
            User::factory ()->count ( $additionalUsersCount )->create ();
        }
    }
}
