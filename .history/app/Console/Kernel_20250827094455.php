<?php

namespace App\Console;

use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Notifications\Notification;

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

        $schedule->command('queue:work --stop-when-empty')->everyMinute();
        $schedule->call(function () {
        $companies = Tenant::where('trial_ends_at', '<=', Carbon::now()->addDays(5))
                            ->where('trial_ends_at', '>', Carbon::now())
                            ->get();

        foreach ($companies as $company) {
            // E-posta gönderin veya başka bir uyarı işlemi yapın
            // Mail::to($company->contact_email)->send(new TrialWarningMail($company));
            \Log::info("Firma {$company->name} için deneme süresi bitimine 5 gün veya daha az kaldı.");
        }
    })->daily(); // Her gün gece yarısı çalıştır
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
