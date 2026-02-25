<?php

declare(strict_types=1);

namespace App\Console;

use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

use function base_path;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('delete:inactive_users')->daily();
        $schedule->command('app:usage-charge')->daily();
        $schedule->command('app:clear-webhook-history')->hourly();
        $schedule->command('app:reset-page-views')
            ->when(function () {
                // Calculate the time until the end of the current month
                $now = Carbon::now();
                $endOfMonth = $now->copy()->endOfMonth();
                $timeUntilEndOfMonth = $now->diffInSeconds($endOfMonth);

                // Check if there are approximately 5 minutes left
                return $timeUntilEndOfMonth <= 300 && $timeUntilEndOfMonth >= 295;
            });
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
