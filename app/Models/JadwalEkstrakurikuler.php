<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalEkstrakurikuler extends Model
{
    use HasFactory;

    protected $table = 'jadwal_ekstrakurikuler';

    protected $fillable = [
        'ekstrakurikuler_id',
        'hari',
        'jam_mulai',
        'jam_selesai',
    ];


    public function sesiAbsenEkstrakurikuler()
    {
        return $this->hasMany(SesiAbsensiEkstrakurikuler::class, 'jadwal_id');
    }

    public function ekstrakurikuler()
    {
        return $this->belongsTo(Ekstrakurikuler::class, 'ekstrakurikuler_id');
    }
}
