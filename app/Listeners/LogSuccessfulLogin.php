<?php

namespace App\Listeners;

use App\Models\login_log;
use Illuminate\Auth\Events\Login;
use Carbon\Carbon;
use App\Models\LoginLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogSuccessfulLogin
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        login_log::updateOrCreate(
            ['user_id' => $event->user->id],
            [
                'login_at'   => now(),
                'logout_at'  => null,
                'status'     => 'online',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]
        );
    }
}
