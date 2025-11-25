<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class software extends Model
{
    protected $table = 'software';

    protected $fillable = [
        'nama',
        'versi',
        'kategori',
        'lisensi',
        'developer',
        'tgl_rilis',
        'deskripsi',
    ];
    
    use SoftDeletes;
    protected $dates = ['deleted_at'];

    protected $casts = [
        'tgl_rilis' => 'date',
    ];

    public function penginstalans(): HasMany
    {
        return $this->hasMany(Penginstalan::class, 'software_id');
    }
}
