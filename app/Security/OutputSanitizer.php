<?php

namespace App\Security;

class OutputSanitizer
{
    protected static array $hidden = [
        'id',
        'user_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public static function clean(array $data): array
    {
        return collect($data)->except(self::$hidden)->toArray();
    }

    public static function cleanMany($rows): array
    {
        return collect($rows)
            ->map(fn($row) => self::clean($row))
            ->values()
            ->toArray();
    }
}
