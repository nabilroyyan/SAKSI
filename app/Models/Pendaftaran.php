<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Ekstrakurikuler;
use App\Models\Kelas;

class Pendaftaran extends Model
{
    protected $table = 'pendaftaran';
    protected $fillable = [
        'users_id',
        'ekstrakurikuler_id',
        'kelas_siswa_id',
        'nama_lengkap',
        'no_telepon',
        'alasan',
        'nomer_wali',
        'status_validasi',
        'validator_id',
        'catatan_pembina',
        'tanggal_pendaftaran',
        'tanggal_validasi',
        'tanggal_berhenti',
    ];

    // Define relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'users_id', 'id');
    }

    public function ekstrakurikuler()
    {
        return $this->belongsTo(Ekstrakurikuler::class, 'ekstrakurikuler_id', 'id');
    }

    public function validator()
    {
        return $this->belongsTo(User::class, 'validator_id');
    }

    public function absensiEkstrakurikuler()
    {
        return $this->hasMany(AbsensiEkstrakurikuler::class, 'pendaftaran_id');
    }

    public function kelasSiswa()
    {
        return $this->belongsTo(KelasSiswa::class, 'kelas_siswa_id');
    }

    public function kelas()
    {
        return $this->hasOneThrough(
            Kelas::class,
            KelasSiswa::class,
            'id',             // kelas_siswa.id
            'id',             // kelas.id
            'kelas_siswa_id', // pendaftaran.kelas_siswa_id
            'id_kelas'        // kelas_siswa.id_kelas
        );
    }

    public function siswa()
    {
        return $this->hasOneThrough(
            Siswa::class,
            KelasSiswa::class,
            'id',               // kelas_siswa.id
            'id',               // siswa.id
            'kelas_siswa_id',   // pendaftaran.kelas_siswa_id
            'id_siswa'          // kelas_siswa.id_siswa
        );
    }
}
