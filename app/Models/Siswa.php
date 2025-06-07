<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    protected $table = 'siswa';

    // app/Models/Siswa.php
    protected $fillable = [
        'nis_nip',
        'nama_siswa',
        'email',
        'tempat',
        'tanggal_lahir',
        'kode',
        'jenis_kelamin',
        'agama',
        'no_telepon',
        'tahun_masuk',
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'nis_nip', 'nis_nip');
    }
    public function kelas()
    {
        return $this->belongsToMany(Kelas::class, 'kelas_siswa', 'id_siswa', 'id_kelas')
                    ->withPivot('status', 'is_active', 'tahun_ajaran')->withTimestamps();
    }

    public function kelasAktif()
    {
        return $this->hasMany(KelasSiswa::class, 'id_siswa')->where('is_active', 'aktif');
    }
        public function kelasSiswa()
    {
        return $this->hasOne(KelasSiswa::class, 'id_siswa')->latestOfMany();
    }
    public function pelanggarans()
    {
        return $this->hasMany(Pelanggaran::class, 'id_siswa');
    }


}