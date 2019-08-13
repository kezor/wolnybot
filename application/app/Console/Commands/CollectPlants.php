<?php

namespace App\Console\Commands;

use App\Player;
use App\Service\GameService;
use Illuminate\Console\Command;

class CollectPlants extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fields:collect';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Collect all available plants';

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
            $gameService = new GameService($player);

            $gameService->updateSpacesData();
            $gameService->collectReady();
        }
    }
}
