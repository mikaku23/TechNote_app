<?php

namespace App\Security;

class RoleGuard
{
    protected static array $rules = [
        'mahasiswa' => [
            'penginstalan',
            'penginstalan_status',
            'waktu',
            'greeting',
            'contact',
            'kampus_trigger',
            'rekap',
            'self_query',
            'stmik'
        ],
        'dosen' => [
            'perbaikan',
            'perbaikan_status',
            'waktu',
            'greeting',
            'contact',
            'kampus_trigger',
            'rekap',
            'self_query',
            'stmik'
        ],
    ];

    public static function allowed(string $role, string $intent): bool
    {
        if (!isset(self::$rules[$role])) {
            return false;
        }

        return in_array($intent, self::$rules[$role]);
    }
}
