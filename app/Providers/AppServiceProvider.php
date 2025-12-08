<?php

namespace App\Providers;

use Carbon\Carbon;
use App\Models\contact;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        view()->composer('*', function ($view) {
            $recentMessages = contact::where('created_at', '>=', Carbon::now()->subDays(3))
                ->latest()
                ->get();

            $view->with('recentMessages', $recentMessages);
        });
        // set locale Laravel
        config(['app.locale' => 'id']);

        // set timezone
        date_default_timezone_set('Asia/Jakarta');

        // set locale Carbon
        Carbon::setLocale('id');
        config(['app.locale' => 'id']);
        setlocale(LC_TIME, 'IND');
    }
}
