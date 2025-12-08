<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'estimasi',
        'user_id',
    ];
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    
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
    protected static function booted()
    {
        static::deleting(function ($perbaikan) {
            if ($perbaikan->isForceDeleting()) return;

            // Soft delete rekaps terkait
            $perbaikan->rekaps()->get()->each(function ($r) {
                $r->delete();
            });
        });

        static::restoring(function ($perbaikan) {
            // Pulihkan rekaps terkait
            $perbaikan->rekaps()->withTrashed()->get()->each(function ($r) {
                $r->restore();
                $r->update(['status' => 'tersedia']);
            });
        });
    }
}
