<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Maintenance extends Model
{
    protected $fillable = ['is_active', 'ends_at', 'reason', 'created_by'];

    protected $casts = [
        'is_active' => 'boolean',
        'ends_at' => 'datetime',
    ];

    public static function active()
    {
        return self::where('is_active', true)
            ->where('ends_at', '>', now())
            ->latest()
            ->first();
    }

    public function remainingSeconds()
    {
        return $this->ends_at ? max(0, now()->diffInSeconds($this->ends_at, false)) : 0;
    }
}
