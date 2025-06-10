<?php

namespace App\Imports;

use App\Models\Siswa;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SiswaImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $ttl = explode(',', $row['tempat_tanggal_lahir']);
        $tempat = isset($ttl[0]) ? trim($ttl[0]) : null;

        $tanggal_lahir = null;
        if (isset($ttl[1])) {
            $tanggal_string = trim($ttl[1]);
            $formats = ['d-m-Y', 'd/m/Y', 'Y-m-d', 'd.m.Y'];
            foreach ($formats as $format) {
                try {
                    $tanggal_lahir = \Carbon\Carbon::createFromFormat($format, $tanggal_string);
                    break;
                } catch (\Exception $e) {
                    continue;
                }
            }
        }

        // Validasi kolom penting
        if (
            empty($row['nis']) || empty($row['nama']) || empty($row['email']) ||
            empty($row['telepon']) || !$tempat || !$tanggal_lahir
        ) {
            return null;
        }

        return new Siswa([
            'nis_nip'        => $row['nis'],
            'nama_siswa'     => $row['nama'],
            'email'          => $row['email'],
            'no_telepon'     => $row['telepon'],
            'tempat'         => $tempat,
            'tanggal_lahir'  => $tanggal_lahir,
            'jenis_kelamin'  => strtolower($row['jenis_kelamin']),
            'agama'          => strtolower($row['agama']),
            'tahun_masuk'    => $row['tahun_masuk'],
            'status'         => 'aktif',
            'kode'           => $row['kelas'],
        ]);
    }
}
