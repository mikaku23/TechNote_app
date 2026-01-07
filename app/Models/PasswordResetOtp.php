<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PasswordResetOtp extends Model
{
    protected $table = 'password_reset_otps';
    protected $fillable = ['user_id', 'otp_hash', 'used', 'attempts', 'expires_at'];
    protected $casts = [
        'used' => 'boolean',
        'expires_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
