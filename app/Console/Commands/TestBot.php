<?php

namespace App\Console\Commands;

use App\Player;
use App\Service\GameService;
use Illuminate\Console\Command;

class TestBot extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bot:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $players = Player::where('active', true)->get();

        foreach ($players as $player){
            echo 'Working with player"'.$player->username.'" on server id: '.$player->server_id.PHP_EOL;
            $gameService = new GameService($player);

            $gameService->updateFields();
            $gameService->collectReady();
            $gameService->updateStock();
            $gameService->seed();
        }
    }
}
