<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */ 
    public function run(): void
    {

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'view dashboard',

            'view siswa',
            'view data-siswa',
            'view kelas-siswa',
            'tambah siswa',
            'edit siswa',
            'hapus siswa',
            'tambah kelas-siswa',

            'view jurusan',
            'tambah jurusan',
            'edit jurusan',
            'hapus jurusan',

            'view kelas',
            'tambah kelas',
            'edit kelas',
            'detail kelas',
            'hapus kelas',
            'naik kelas',
            'hapus-siswa kelas',

            'view bk',
            'assign bk',

            'view skor-pelanggaran',
            'tambah skor-pelanggaran',
            'hapus skor-pelanggaran',

            'view kategori-tindakan',
            'tambah kategori-tindakan',
            'hapus kategori-tindakan',

            'view pelanggaran',
            'tambah pelanggaran',
            'edit pelanggaran',
            'hapus pelanggaran',

            'view monitoring-pelanggaran',

            'view catatan-absensi',
            'view input-absensi',
            'view riwayat-absensi',

            'view user',
            'tambah user',
            'edit user',
            'hapus user',

            'view role',
            'tambah role',
            'edit role',
            'manage role',
            'hapus role',

            'view permission',
            'tambah permission',
            'update permission',
            'edit permission',
            'hapus permission',
            'manage permissions',

            'view validasi surat',

        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $superadmin = Role::firstOrCreate(['name' => 'superadmin']);
        $bk = Role::firstOrCreate(['name' => 'bk']);
        $tatip = Role::firstOrCreate(['name' => 'tatip']);
        $sekretaris = Role::firstOrCreate(['name' => 'sekretaris']);
        $walikelas = Role::firstOrCreate(['name' => 'walikelas']);



        $superadmin->givePermissionTo([
            'view dashboard',

            'view siswa',
            'view data-siswa',
            'view kelas-siswa',
            'tambah siswa',
            'edit siswa',
            'hapus siswa',
            'tambah kelas-siswa',

            'view jurusan',
            'tambah jurusan',
            'edit jurusan',
            'hapus jurusan',

            'view kelas',
            'tambah kelas',
            'edit kelas',
            'detail kelas',
            'hapus kelas',
            'naik kelas',
            'hapus-siswa kelas',

            'view bk',
            'assign bk',

            'view skor-pelanggaran',
            'tambah skor-pelanggaran',
            'hapus skor-pelanggaran',

            'view kategori-tindakan',
            'tambah kategori-tindakan',
            'hapus kategori-tindakan',

            'view pelanggaran',
            'tambah pelanggaran',
            'edit pelanggaran',
            'hapus pelanggaran',

            'view monitoring-pelanggaran',

            'view catatan-absensi',
            'view input-absensi',
            'view riwayat-absensi',

            'view user',
            'tambah user',
            'edit user',
            'hapus user',

            'view role',
            'tambah role',
            'edit role',
            'manage role',
            'hapus role',

            'view permission',
            'tambah permission',
            'update permission',
            'edit permission',
            'hapus permission',
            'manage permissions',
            'view validasi surat',
        ]);

        $bk->givePermissionTo([
            'view dashboard',

            'view siswa',
            'view data-siswa',
            'view kelas-siswa',

            'view kelas',
            'detail kelas',

            'view bk',

            'view skor-pelanggaran',

            'view kategori-tindakan',

            'view pelanggaran',

            'view monitoring-pelanggaran',

            'view catatan-absensi',
            'view validasi surat'
        ]);
        
    
    }
}