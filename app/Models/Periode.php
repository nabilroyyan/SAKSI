<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Periode extends Model
{
    protected $table = 'periode';

    protected $fillable = ['tahun', 'semester', 'is_active'];
}
