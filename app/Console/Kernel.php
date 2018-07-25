<?php

namespace App\Console;

use App\Console\Commands\AddPlayer;
use App\Console\Commands\CollectPlants;
use App\Console\Commands\DisableTutorial;
use App\Console\Commands\SeedPlants;
use App\Console\Commands\TestBot;
use App\Console\Commands\UpdateFields;
use App\Console\Commands\UpdateStock;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        sleep(rand(23, 194));

        $now = Carbon::now()->format('Y-m-d');
        // run at work :)
        $schedule->command('game:process')
            ->everyTenMinutes()
            ->appendOutputTo(storage_path('logs/scheduler_' . $now . '.log'))
            ->withoutOverlapping();


    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');
        require base_path('routes/console.php');
    }
}
