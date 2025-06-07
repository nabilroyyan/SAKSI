<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotifPendaftaran extends Model
{
    protected $table = 'notif_pendaftaran';

    protected $fillable = [
        'user_id',
         'title', 
         'message', 
         'is_read',
         'receiver_id',
        ];

        public function user()
        {
            return $this->belongsTo(User::class);
        }
    
        public function receiver()
        {
            return $this->belongsTo(User::class, 'receiver_id');
        }
}
