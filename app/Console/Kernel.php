<?php

namespace App\Console;

use App\Console\Commands\AddPlayer;
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
        DisableTutorial::class,
        AddPlayer::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // run at work :)
        $schedule->command('bot:test')
            ->weekdays()
            ->everyThirtyMinutes()
            ->between('7:45', '16:08');

        // run once/twice at evening at weekdays
        $schedule->command('bot:test')
            ->weekdays()
            ->everyThirtyMinutes()
            ->between('20:00', '21:30');

        // Have two periods on saturday
        $schedule->command('bot:test')
            ->saturdays()
            ->everyThirtyMinutes()
            ->between('8:30', '10:15');

        $schedule->command('bot:test')
            ->saturdays()
            ->everyThirtyMinutes()
            ->between('18:20', '20:15');

        // Have two periods on sunday
        $schedule->command('bot:test')
            ->sundays()
            ->everyThirtyMinutes()
            ->between('9:30', '10:55');

        $schedule->command('bot:test')
            ->sundays()
            ->everyThirtyMinutes()
            ->between('17:10', '19:25');
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}
