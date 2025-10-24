<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class notification extends Model
{
    protected $table = 'notifications';

    protected $fillable = [
        'title',
        'message',
        'is_read',
        'user_id',
        'perbaikan_id',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function perbaikan(): BelongsTo
    {
        return $this->belongsTo(Perbaikan::class, 'perbaikan_id');
    }
}
