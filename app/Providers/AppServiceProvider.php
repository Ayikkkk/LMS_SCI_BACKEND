<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // ============================================================
        // RATE LIMITER
        // Naikkan dari default 60 → 300 req/menit untuk API umum
        // Siswa aktif kuis bisa kirim banyak request (log, timer, dll)
        // ============================================================
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(300)->by(
                optional($request->user())->id ?: $request->ip()
            );
        });

        // ============================================================
        // SCHEDULED JOBS
        // Jalankan via cron: * * * * * php artisan schedule:run
        // ============================================================
        $this->callAfterResolving(Schedule::class, function (Schedule $schedule) {
            // Bersihkan quiz activity logs > 30 hari — setiap hari jam 02:00 WIB
            $schedule->command('quiz:clean-logs')
                ->dailyAt('02:00')
                ->runInBackground()
                ->withoutOverlapping();

            // Bersihkan file cache proxy-image > 30 hari — setiap hari jam 03:00 WIB
            $schedule->command('proxy-images:cleanup --days=30')
                ->dailyAt('03:00')
                ->runInBackground()
                ->withoutOverlapping();
        });
    }
}
