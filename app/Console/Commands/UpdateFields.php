<?php

namespace App\Console\Commands;

use App\Player;
use App\Service\GameService;
use Illuminate\Console\Command;

class UpdateFields extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fields:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update all fields in database';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $players = Player::where('active', true)->get();

        /** @var Player $player */
        foreach ($players as $player){
            echo 'Working with player on server: ' . $player->server_id . PHP_EOL;

            $gameService = new GameService($player);

            $gameService->updateBuildings();
        }
    }
}
