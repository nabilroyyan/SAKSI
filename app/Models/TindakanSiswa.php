<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TindakanSiswa extends Model
{
    protected $table = 'tindakan_siswa';
    protected $fillable = [
        'catatan', 
        'status', 
        'tanggal',
        'kelas_siswa_id', 
        'id_siswa', 
        'id_tindakan'];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'id_siswa');
    }

    public function kategoriTindakan()
    {
        return $this->belongsTo(KategoriTindakan::class, 'id_tindakan');
    }

       // Relasi ke kelas_siswa
    public function kelasSiswa()
    {
        return $this->belongsTo(KelasSiswa::class, 'kelas_siswa_id');
    }

}
