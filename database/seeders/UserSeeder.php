<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User; // pastikan ini di-import

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'superadmin',
            'email' => 'superadmin@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('superadmin123'),
            'nis_nip' => '11111111',
        ])->assignRole('superadmin');

        User::create([
            'name' => 'tatip',
            'email' => 'tatip@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('tatip123'),
            'nis_nip' => '88888888',
        ])->assignRole('tatip');
        
        User::create([
            'name' => 'bk',
            'email' => 'bk@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('bk123'),
            'nis_nip' => '77777777',
        ])->assignRole('bk');
        
        User::create([
            'name' => 'sekretaris',
            'email' => 'sekretaris@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('sekretaris123'),
            'nis_nip' => '66666666',
        ])->assignRole('sekretaris');
        
        User::create([
            'name' => 'walikelas',
            'email' => 'walikelas@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('walikelas123'),
            'nis_nip' => '55555555',
        ])->assignRole('walikelas');

    }
}