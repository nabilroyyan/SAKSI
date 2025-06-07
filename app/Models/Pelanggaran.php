<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pelanggaran extends Model
{
    use HasFactory;
    protected $table = 'pelanggaran';

    protected $fillable = [
        'ket_pelanggaran',
        'bukti_pelanggaran',
        'tanggal',
        'id_siswa',
        'kelas_siswa_id',
        'id_users',
        'id_skor_pelanggaran'
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

    public function skor_pelanggaran()
    {
        return $this->belongsTo(Skor_Pelanggaran::class, 'id_skor_pelanggaran');
    }
}