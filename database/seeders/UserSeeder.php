<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
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
            'password' => Hash::make('123'),
            'nis_nip' => '11111111',
        ])->assignRole('superadmin');

        User::create([
            'name' => 'tatip',
            'email' => 'tatip@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('123'),
            'nis_nip' => '88888888',
        ])->assignRole('tatip');
        
        User::create([
            'name' => 'bk 1',
            'email' => 'bk1@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('123'),
            'nis_nip' => '77777777',
        ])->assignRole('bk');
        
        User::create([
            'name' => 'sekretaris x tkj 1',
            'email' => 'xtkj1@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('123'),
            'nis_nip' => '66666666',
        ])->assignRole('sekretaris');
        
        User::create([
            'name' => 'walikelas',
            'email' => 'walikelas@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('walikelas123'),
            'nis_nip' => '55555555',
        ])->assignRole('walikelas');
        
        User::create([
            'name' => 'sekretaris xi tkj 2',
            'email' => 'xtkj2@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('123'),
            'nis_nip' => '66666666',
        ])->assignRole('sekretaris');
       
        User::create([
            'name' => 'sekretaris x ph 1',
            'email' => 'xph1@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('123'),
            'nis_nip' => '66666666',
        ])->assignRole('sekretaris');

        User::create([
            'name' => 'bk 2',
            'email' => 'bk2@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('123'),
            'nis_nip' => '55555555',
        ])->assignRole('bk');

        DB::table('pengaturan_tindakan')->insert([
            'batas_skor' => 100, 
        ]);
    }
}