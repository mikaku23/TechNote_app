<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $table = 'Users';

    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'nama',
        'nim',
        'nip',
        'username',
        'password',
        'no_hp',
        'foto',
        'qr_code',
        'qr_url',
        'security_question',
        'security_answer',
        'old_password',
        'last_password_changed_at',
        'role_id',
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

    protected $casts = [
        'password' => 'hashed',
        'security_answer' => 'hashed',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function role(): BelongsTo
    {
        return $this->belongsTo(role::class, 'role_id');
    }

    /**
     * Relasi ke Penginstalan (jika ada)
     */
    public function penginstalans(): HasMany
    {
        return $this->hasMany(Penginstalan::class, 'user_id');
    }

    public function perbaikans(): HasMany
    {
        return $this->hasMany(Perbaikan::class, 'user_id');
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class, 'user_id');
    }

    public function UserActivities(): HasMany
    {
        return $this->hasMany(UserActivity::class, 'user_id');
    }

    /**
     * Relasi ke Notification (jika ada)
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'user_id');
    }

    public function loginLogs(): HasMany
    {
        return $this->hasMany(login_log::class);
    }

    public function passwordResetOtps(): HasMany
    {
        return $this->hasMany(PasswordResetOtp::class, 'user_id');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */


    
}
