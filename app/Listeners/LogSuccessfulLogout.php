<?php

namespace App\Listeners;

use App\Models\login_log;
use Illuminate\Auth\Events\Logout;
use Carbon\Carbon;

class LogSuccessfulLogout
{
    public function __construct()
    {
        //
    }

    public function handle(Logout $event)
    {
        if (! $event->user) {
            return;
        }

        // waktu sekarang sesuai Asia/Jakarta
        $now = Carbon::now('Asia/Jakarta');

        // cari record login terakhir untuk user ini yang masih online (belum logout)
        $log = login_log::where('user_id', $event->user->id)
            ->where('status', 'online')
            ->whereNull('logout_at')
            ->latest('login_at')
            ->first();

        if (! $log) {
            // tidak ada record online — tidak perlu apa-apa
            return;
        }

        // update hanya record yang ditemukan (login N -> logout N)
        $log->update([
            'logout_at' => $now,
            'status'    => 'offline',
        ]);
    }
}
