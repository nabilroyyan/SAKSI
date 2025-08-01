<?php

namespace App\Models;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;


class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    
    protected $fillable = [
        'name',
        'email',
        'password',
        'nis_nip',
        'email_verified_at', 
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

      public function kelasYangDiampuBk()
    {
        return $this->belongsToMany(
            Kelas::class,
            'bk_kelas',     // nama pivot table
            'id_bk',        // foreign key untuk User di pivot table
            'id_kelas'      // foreign key untuk Kelas di pivot table
        )->withTimestamps();
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'nis_nip', 'nis_nip');
    }

    public function ekstrakurikuler()
    {
        return $this->hasMany(Ekstrakurikuler::class, 'id_users'); // Sesuaikan foreign key
    }

    public function pendaftaran()
    {
        return $this->hasMany(Pendaftaran::class);
    }
}
