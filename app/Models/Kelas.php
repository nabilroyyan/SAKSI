<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    use HasFactory;

    protected $table = 'kelas';

    protected $fillable = [
        'kelas',
        'id_jurusan',
        'id_users',
        'tingkat',
        'stt'
    ];

    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class, 'id_jurusan');
    }
   public function kelasSiswa()
    {
        return $this->hasMany(KelasSiswa::class, 'id_kelas');
    }


  // Di Model Kelas.php
    public function siswa()
    {
        return $this->belongsToMany(Siswa::class, 'kelas_siswa', 'id_kelas', 'id_siswa')
                    ->withPivot('status','tahun_ajaran', 'is_active')
                    ->withTimestamps(); // Wajib tambahkan ini!
    }

     public function bkPengampu()
    {
        return $this->belongsToMany(
            User::class,
            'bk_kelas',     // nama pivot table
            'id_kelas',     // foreign key untuk Kelas di pivot table
            'id_bk'         // foreign key untuk User di pivot table
        )->withTimestamps();
    }

        public function bk()
    {
        return $this->hasMany(BkKelas::class, 'id_kelas');
    }


}