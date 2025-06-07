<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prestasi extends Model
{
    protected $table = 'prestasi';

    protected $fillable = [
        'pendaftaran_id',
        'siswa_id',
        'ekstrakurikuler_id',
        'nama_kegiatan',
        'peringkat',
        'tanggal_kejuaraan',
        'tingkat_kejuaraan',
        'deskripsi',
        'foto_prestasi',
    ];

    public function pendaftaran()
    {
        return $this->belongsTo(Pendaftaran::class);
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function ekstrakurikuler()
    {
        return $this->belongsTo(Ekstrakurikuler::class);
    }
}
