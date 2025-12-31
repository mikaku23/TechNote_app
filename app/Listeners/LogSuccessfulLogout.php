<?php

namespace App\Listeners;

use App\Models\login_log;
use Illuminate\Auth\Events\Logout;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogSuccessfulLogout
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
    public function handle(Logout $event)
    {
        if (!$event->user) {
            return;
        }

        login_log::where('user_id', $event->user->id)
            ->where('status', 'online')
            ->update([
                'logout_at' => now(),
                'status'    => 'offline',
            ]);
    }
}
