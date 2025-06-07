<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SesiAbsensiEkstrakurikuler extends Model
{
    protected $table = 'sesi_absen_ekstrakurikuler';

    protected $fillable = [
        'jadwal_id',
        'guru_pembina_id',
        'waktu_buka',
        'waktu_tutup',
        'is_active',
        'absensi_telah_disimpan',

    ];

    public function jadwal()
    {
        return $this->belongsTo(JadwalEkstrakurikuler::class, 'jadwal_id');
    }

    public function guruPembina()
    {
        return $this->belongsTo(User::class, 'guru_pembina_id');
    }


    public function absensiEkstrakurikuler()
    {
        // Mengurutkan berdasarkan nama siswa melalui relasi pendaftaran dan user
        return $this->hasMany(AbsensiEkstrakurikuler::class, 'sesi_absen_ekstrakurikuler_id')
            ->join('pendaftaran', 'absensi_ekstrakurikuler.pendaftaran_id', '=', 'pendaftaran.id')
            ->join('users', 'pendaftaran.users_id', '=', 'users.id')
            ->orderBy('users.name', 'asc')
            ->select('absensi_ekstrakurikuler.*'); // Penting untuk select kolom dari tabel absensi
    }
}
