<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ekstrakurikuler extends Model
{
    use HasFactory;

    protected $table = 'ekstrakurikuler';

    protected $fillable = [
        'nama_ekstrakurikuler',
        'gambar',
        'adwal',
        'deskripsi',
        'id_kategori',
        'jam_mulai',
        'jam_selesai',
        'lokasi',
        'periode',
        'jenis',
        'kuota',
        'id_users',
    ];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'id_kategori');
    }

    public function siswas()
    {
        return $this->belongsToMany(User::class, 'pendaftaran', 'ekstrakurikuler_id', 'users_id')
            // PENTING: Hanya hitung siswa yang status pendaftarannya 'diterima'
            ->wherePivot('status_validasi', 'diterima');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'id_users');
    }
    public function pendaftarans()
    {
        return $this->hasMany(Pendaftaran::class);
    }
    public function jadwals()
    {
        return $this->hasMany(JadwalEkstrakurikuler::class, 'ekstrakurikuler_id');
    }


    public function sesiAbsen()
    {
        return $this->hasMany(SesiAbsensiEkstrakurikuler::class, 'jadwal_id');
    }
    public function pendaftarAktif()
    {
        // Menggunakan relasi pendaftarans() yang sudah didefinisikan di atas
        return $this->pendaftarans()->where('status_validasi', 'diterima')->count();
    }

    public function prestasi()
    {
        return $this->hasMany(Prestasi::class);
    }

}