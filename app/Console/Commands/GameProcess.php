<?php

namespace App\Console\Commands;

use App\Repository\PlayerRepository;
use App\Repository\TaskRepository;
use App\Service\GameService;
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
        $players = PlayerRepository::getAllActive();

        foreach ($players as $player){

            $gameService = new GameService($player);

            $task = TaskRepository::getPlayerTaskReadyToRun($player);



        }
    }
}