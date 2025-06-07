<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Siswa;

class SiswaSeeder extends Seeder
{
    public function run(): void
    {
        $data = [];

        // Daftar jurusan
        $jurusan = [
            'DESAIN DAN PRODUKSI BUSANA',
            'PERHOTELAN',
            'MANAJEMEN PERKANTORAN',
            'BISNIS DIGITAL',
            'REKAYASA PERANGKAT LUNAK',
            'PRODUKSI FILM',
            'TEKNIK KOMPUTER JARINGAN',
        ];

        // Generate kombinasi kelas (X, XI, XII) + jurusan + rombel 1 & 2
        $kode = [];
        foreach (['X', 'XI', 'XII'] as $tingkat) {
            foreach ($jurusan as $jrs) {
                for ($rombel = 1; $rombel <= 2; $rombel++) {
                    $kode[] = "{$tingkat} {$jrs} {$rombel}";
                }
            }
        }

        $agamaList = ['islam', 'protestan', 'katolik', 'hindu', 'budha', 'khonghucu'];


        for ($i = 1; $i <= 50; $i++) {
            $data[] = [
                'nama_siswa' => 'Siswa ' . $i,
                'nis_nip' => 'NISN' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'email' => 'siswa' . $i . '@example.com',
                'tempat' => 'Kota ' . $i,
                'tanggal_lahir' => now()->subYears(rand(15, 18))->subDays(rand(0, 365))->format('Y-m-d'),
                'kode' => $kode[array_rand($kode)],
                'jenis_kelamin' => rand(0, 1) ? 'laki-laki' : 'perempuan',
                'agama' => $agamaList[array_rand($agamaList)],
                'no_telepon' => '0812' . rand(10000000, 99999999),
                'tahun_masuk' => '20' . rand(18, 23),
                'status' => rand(0, 1) ? 'aktif' : 'tidak_aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        Siswa::insert($data);
    }
}