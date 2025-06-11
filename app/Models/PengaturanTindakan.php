<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengaturanTindakan extends Model
{
    protected $table = 'pengaturan_tindakan';
    protected $fillable = ['batas_skor'];
}
