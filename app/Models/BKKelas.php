<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BKKelas extends Model
{
     use HasFactory;

    protected $table = 'bk_kelas';

    protected $fillable = [
        'id_bk',
        'id_kelas',
    ];
}
