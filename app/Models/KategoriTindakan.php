<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KategoriTindakan extends Model
{
    protected $table = 'kategori_tindakan';

    protected $fillable = [
        'nama_tindakan',
    ];
}
