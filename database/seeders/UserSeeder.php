<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'superadmin',
            'svp',
            'vp',
            'admin_divisi',
            'koordinator_proyek',
            'admin_divisi', // Koor Satu
            'admin_divisi', // Koor Dua
            'admin_divisi'  // Koor Tiga
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

        foreach ($roles as $index => $role) {
            User::factory()->create([
                'name'         => $names[$index],
                'username'     => $users[$index],
                'email'        => $emails[$index],
                'role'         => $role,
                'password'     => Hash::make($passwords[$index]),
                'path_profile' => $defaultProfilePath,
            ]);
        }
    }
}
