<?php

namespace App\Listeners;

use App\Models\login_log;
use Illuminate\Auth\Events\Login;
use Carbon\Carbon;

class LogSuccessfulLogin
{
    public function __construct()
    {
        //
    }

    public function handle(Login $event): void
    {
        // waktu sekarang sesuai Asia/Jakarta
        $now = Carbon::now('Asia/Jakarta');

        // jaga-jaga bila event Login terpanggil berkali-kali dalam 5 detik:
        $recent = login_log::where('user_id', $event->user->id)
            ->where('ip_address', request()->ip())
            ->where('user_agent', request()->userAgent())
            ->where('login_at', '>=', $now->copy()->subSeconds(5))
            ->exists();

        if ($recent) {
            return; // sudah tercatat sangat baru — abaikan duplikat
        }

        // buat record login baru — setiap login tercatat sebagai baris baru
        login_log::create([
            'user_id'    => $event->user->id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'login_at'   => $now,
            'logout_at'  => null,
            'status'     => 'online',
        ]);
    }
}
