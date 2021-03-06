<?php

namespace App\Console\Commands;

use App\Connector\WolniFarmerzyConnector;
use App\Player;
use App\Service\GameService;
use Illuminate\Console\Command;

class UpdateStock extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stock:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update stock';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $players = Player::where('active', true)->get();

        /** @var Player $player */
        foreach ($players as $player) {
            echo 'Working with player on server: ' . $player->server_id . PHP_EOL;
            $gameService = new GameService($player, new WolniFarmerzyConnector());
            $gameService->updateStock();
        }
    }
}
