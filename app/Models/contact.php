<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class contact extends Model
{
    protected $table = 'contacts';

    protected $fillable = [
        'nama',
        'email',
        'pesan',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
