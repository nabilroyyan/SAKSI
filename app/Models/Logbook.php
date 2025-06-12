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
        'kegiatan',
        'tanggal',
        'jam_mulai',
        'jam_selesai',
        'foto_kegiatan',
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
