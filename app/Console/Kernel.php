<?php
namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
/**
* Define the application's command schedule.
*/
protected function schedule(Schedule $schedule): void
{
// Contoh: jalankan perintah notify:wa setiap menit
$schedule->command('notify:wa')->everyMinute();

        $schedule->command('autofinish:run')
            ->everyMinute()
            ->appendOutputTo(storage_path('logs/autofinish.log'))
            ->withoutOverlapping()
            ->onOneServer()
            ->timezone('Asia/Jakarta')
            ->runInBackground();
    }

/**
* Register the commands for the application.
*/
protected function commands(): void
{
$this->load(__DIR__.'/Commands');

require base_path('routes/console.php');
}
}