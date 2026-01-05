<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class login_log extends Model
{
    protected $table = 'login_logs';

    protected $fillable = [
        'user_id',
        'ip_address',
        'user_agent',
        'status',
        'logout_at',
        'login_at',
    ];

    protected $casts = [
        'login_at'  => 'datetime',
        'logout_at' => 'datetime',
    ];

    // durasi login (HH:MM:SS)
    public function getDurasiLoginAttribute()
    {
        if (!$this->logout_at) {
            return null; // masih online
        }

        $seconds = $this->login_at->diffInSeconds($this->logout_at);

        return gmdate('H:i:s', $seconds);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function activities()
    {
        return $this->hasMany(UserActivity::class, 'login_log_id');
    }
}
