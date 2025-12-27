<?php

namespace App\Security;

class KeywordGuard
{
    protected static array $blocked = [
        'admin',
        'petugas',
        'database',
        'user lain',
        'id',
        'hapus',
        'edit',
        'update',
    ];

    public static function isBlocked(string $text): bool
    {
        $text = strtolower($text);

        foreach (self::$blocked as $word) {
            if (str_contains($text, $word)) {
                return true;
            }
        }

        return false;
    }
}
