<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Logbook extends Model
{
    use HasFactory;

    protected $table = 'logbook';

    protected $fillable = [
        'users_id',
        'ekstrakurikuler_id',
        'Kegiatan',
        'Tanggal',
        'Jam_mulai',
        'Jam_selesai',
        'Foto_kegiatan',
        'is_locked',
        
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
    }

    public function ekstrakurikuler()
    {
        return $this->belongsTo(Ekstrakurikuler::class, 'ekstrakurikuler_id');
    }

    public function sesiAbsensiEkstrakurikuler()
    {
        return $this->belongsTo(SesiAbsensiEkstrakurikuler::class, 'sesi_id');
    }
}
