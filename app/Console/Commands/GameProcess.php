<?php

namespace App\Console\Commands;

use App\Repository\PlayerRepository;
use App\Repository\TaskRepository;
use App\Service\GameService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GameProcess extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'game:process {--username=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process all players (check tasks, login, do some stuff, logout)';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Start time: ' . Carbon::now()->format('Y-m-d H:i:s'));

        $secondsForSleep = rand(23, 194);

        $this->info('Sleeping for: ' . $secondsForSleep . ' seconds.');

        sleep($secondsForSleep);

        $this->info('Logging for active users.');

        $players = PlayerRepository::getAllActive();

        $this->info('I found: ' . $players->count() . ' players.');
        foreach ($players as $player) {
            $this->info('Starting working with: ' . $player->username);

            $gameService = new GameService($player);
            $this->info('Updating stock...');

            $gameService->updateStock();

            $this->info('Updating buildings data...');
            $gameService->updateBuildings();

            $tasks = TaskRepository::getPlayerTaskReadyToRun($player);

            $this->info('Player ' . $player->username . ' has ' . $tasks->count() . ' active tasks.');

            foreach ($tasks as $task) {
                $this->info('Starting working with task: ' . $task->getJobName() . ' (' . Carbon::now()->format('Y-m-d H:i:s') . ')');
                $gameService->processFarmland($task->getJobObject());
                $this->info('Working with task is done (' . Carbon::now()->format('Y-m-d H:i:s') . ')');
            }
        }
        $this->info('End time: ' . Carbon::now()->format('Y-m-d H:i:s'));
    }
}