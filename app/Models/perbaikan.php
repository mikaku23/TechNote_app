<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class perbaikan extends Model
{
    protected $table = 'perbaikans';

    protected $fillable = [
        'nama',
        'kategori',
        'lokasi',
        'status',
        'keterangan',
        'tgl_perbaikan',
        'user_id',
    ];

    protected $casts = [
        'tgl_perbaikan' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function rekaps(): HasMany
    {
        return $this->hasMany(Rekap::class, 'perbaikan_id');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'perbaikan_id');
    }
}
