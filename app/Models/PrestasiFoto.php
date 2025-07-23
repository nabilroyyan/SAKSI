<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PrestasiFoto extends Model
{
    protected $table = 'prestasi_fotos';
    protected $fillable = ['prestasi_id', 'file_path'];

    public function prestasi()
    {
        return $this->belongsTo(Prestasi::class, 'prestasi_id');
    }
}