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
         // Her gün gece saat 2'de çalışsın
        //$schedule->command('photos:delete-old')
         //       ->dailyAt('02:00')
          //      ->withoutOverlapping();
        
        // Haftalık olarak çalışsın (Pazartesi saat 02:00)
        $schedule->command('photos:delete-old')
                ->weekly()
                ->mondays()
                ->at('02:00')
                ->withoutOverlapping();
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
