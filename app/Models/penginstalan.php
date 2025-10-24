<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class penginstalan extends Model
{
    protected $table = 'penginstalans';

    protected $fillable = [
        'tgl_hapus',
        'tgl_instalasi',
        'status',
        'software_id',
        'user_id',
    ];

    protected $casts = [
        'tgl_hapus' => 'date',
        'tgl_instalasi' => 'date',
    ];

    public function software(): BelongsTo
    {
        return $this->belongsTo(Software::class, 'software_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function rekaps(): HasMany
    {
        return $this->hasMany(Rekap::class, 'penginstalan_id');
    }
}
