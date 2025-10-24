<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class rekap extends Model
{
    protected $table = 'rekaps';

    protected $fillable = [
        'perbaikan_id',
        'penginstalan_id',
    ];

    /**
     * Relasi ke Perbaikan
     */
    public function perbaikan(): BelongsTo
    {
        return $this->belongsTo(Perbaikan::class, 'perbaikan_id');
    }

    /**
     * Relasi ke Penginstalan
     */
    public function penginstalan(): BelongsTo
    {
        return $this->belongsTo(Penginstalan::class, 'penginstalan_id');
    }
}
