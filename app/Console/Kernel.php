<?php

namespace App\Console;

use App\Console\Commands\CollectPlants;
use App\Console\Commands\DisableTutorial;
use App\Console\Commands\SeedPlants;
use App\Console\Commands\TestBot;
use App\Console\Commands\UpdateFields;
use App\Console\Commands\UpdateStock;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        TestBot::class,
        UpdateStock::class,
        SeedPlants::class,
        CollectPlants::class,
        UpdateFields::class,
        DisableTutorial::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
