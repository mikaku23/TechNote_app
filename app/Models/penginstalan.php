<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class penginstalan extends Model
{
    use SoftDeletes;

    protected $table = 'penginstalans';

    const DELETED_AT = 'tgl_hapus';

    protected $dates = ['tgl_instalasi', 'tgl_hapus'];

    protected $fillable = [
        'tgl_hapus',
        'tgl_instalasi',
        'status',
        'estimasi',
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
