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
            'import-siswa',

            'view jurusan',
            'tambah jurusan',
            'edit jurusan',
            'hapus jurusan',

            'view periode',
            'tambah periode',
            'aktif periode',
            'hapus periode',

            'view kelas',
            'tambah kelas',
            'edit kelas',
            'detail kelas',
            'hapus kelas',
            'naik kelas',
            'update periode',
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
            'detail pelanggaran',

            'view monitoring-pelanggaran',
            'view tindakan-siswa',
            'update tindakan-siswa',
            'berikan tindakan-siswa',
            'detail tindakan-siswa',
            'hapus tindakan-siswa',

            'view catatan-absensi',
            'view input-absensi',
            'view riwayat-absensi',

            'view monitoring-absensi',
            'detail monitoring-absensi',

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
            'terima vaidasi',
            'tolak validasi',

            'tulisan data master',
            'tulisan pelanggaran',
            'tulisan absensi',
            'tulisan user management',

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
            'import-siswa',

            'view jurusan',
            'tambah jurusan',
            'edit jurusan',
            'hapus jurusan',

            'view periode',
            'tambah periode',
            'aktif periode',
            'hapus periode',

            'view kelas',
            'tambah kelas',
            'edit kelas',
            'detail kelas',
            'hapus kelas',
            'naik kelas',
            'update periode',
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
            'detail pelanggaran',

            'view monitoring-pelanggaran',
            'view tindakan-siswa',
            'update tindakan-siswa',
            'berikan tindakan-siswa',
            'detail tindakan-siswa',
            'hapus tindakan-siswa',

            'view monitoring-absensi',
            'detail monitoring-absensi',

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
            'terima vaidasi',
            'tolak validasi',

            'tulisan data master',
            'tulisan pelanggaran',
            'tulisan absensi',
            'tulisan user management',
        ]);

        $bk->givePermissionTo([
            'view dashboard',

            'view bk',

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
            'detail pelanggaran',
            'view tindakan-siswa',
            'update tindakan-siswa',
            'berikan tindakan-siswa',

            'view monitoring-absensi',
            'detail monitoring-absensi',

            'view validasi surat',
            'terima vaidasi',
            'tolak validasi',

            'tulisan pelanggaran',
            'tulisan absensi',
        ]);

        $tatip->givePermissionTo([
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
            'detail pelanggaran',

            'view monitoring-pelanggaran',
            'view tindakan-siswa',

            'tulisan pelanggaran',
        ]);

          $sekretaris->givePermissionTo([
            'view catatan-absensi',
            'view input-absensi',
            'view riwayat-absensi',

            'tulisan absensi',

        ]);
        
        
    
    }
}