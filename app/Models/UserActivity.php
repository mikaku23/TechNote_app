<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserActivity extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'login_log_id',
        'activity',
        'type',
        'created_at',
    ];

    public function loginLog()
    {
        return $this->belongsTo(login_log::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected $casts = [
        'created_at' => 'datetime',
    ];
}
