<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Scheduled jobs — jalankan via: php artisan schedule:run (cron setiap menit)
        $this->callAfterResolving(Schedule::class, function (Schedule $schedule) {
            // Bersihkan quiz activity logs lebih dari 30 hari — setiap hari jam 02:00 WIB
            $schedule->command('quiz:clean-logs')
                ->dailyAt('02:00')
                ->runInBackground()
                ->withoutOverlapping();
        });
    }
}
