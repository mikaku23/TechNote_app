<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class rekap extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];

    protected $table = 'rekaps';

    protected $fillable = [
        'perbaikan_id',
        'penginstalan_id',
        'status',
    ];

   
    /**
     * Relasi ke Perbaikan
     */
    public function perbaikan(): BelongsTo
    {
        return $this->belongsTo(Perbaikan::class, 'perbaikan_id')->withTrashed();
    }

    public function penginstalan(): BelongsTo
    {
        return $this->belongsTo(Penginstalan::class, 'penginstalan_id')->withTrashed();
    }
}
