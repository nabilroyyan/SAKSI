<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Database\Seeders\SiswaSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Support\Facades\Hash;
use Database\Seeders\RolePermissionSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {


        // $this->call([
        //     RolePermissionSeeder::class,
        // ]);
        $this->call([
            RolePermissionSeeder::class,
            UserSeeder::class,
            // SiswaSeeder::class,
        ]);




    }
}