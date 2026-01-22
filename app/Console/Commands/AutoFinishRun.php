<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AutoFinishRun extends Command
{
    protected $signature = 'autofinish:run';
    protected $description = 'Jalankan AutoFinishService (update status, buat QR, kirim WA)';

    public function handle(): int
    {
        try {
            app(\App\Services\AutoFinishService::class)->handleAll();
            $this->info('AutoFinishService executed.');
        } catch (\Throwable $e) {
            Log::error('AutoFinishRun command error: ' . $e->getMessage());
            $this->error('Error: ' . $e->getMessage());
        }

        return 0;
    }
}
