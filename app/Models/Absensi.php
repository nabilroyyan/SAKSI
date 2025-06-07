<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Absensi extends Model
{
    use HasFactory;

    protected $table = 'absensi';

    protected $fillable = [
        'status',
        'foto_surat',
        'hari_tanggal',
        'status_surat',
        'catatan',
        'id_siswa',
        'kelas_siswa_id',
        'id_users',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'id_siswa');
    }

    public function kelasSiswa()
    {
        return $this->belongsTo(KelasSiswa::class, 'kelas_siswa_id');
    }

    public function petugas()
    {
        return $this->belongsTo(User::class, 'id_users');
    }
}
