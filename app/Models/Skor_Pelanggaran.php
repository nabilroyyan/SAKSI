<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Skor_Pelanggaran extends Model
{
    protected $table = 'skor_pelanggaran';

    protected $fillable = [
        'nama_pelanggaran',
        'skor',
        'jenis_pelanggaran',
    ];
}
