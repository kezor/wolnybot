<?php

namespace App\Console\Commands;

use App\Connector\WolniFarmerzyConnector;
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
    protected $signature = 'bot:test {--username=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process all game';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if ($this->option('username')) {
            $players = Player::where('username', $this->option('username'))->get();
        } else {
            $players = Player::where('active', true)->get();
        }

        if ($players->isEmpty()) {
            $this->info('Not active users found');
            return 0;
        }

        foreach ($players as $player) {
            $this->info('###### Working with player "' . $player->username . '" on server id: ' . $player->server_id . ' ######');
            $gameService = new GameService($player, new WolniFarmerzyConnector());
            echo 'Updating fields...' . PHP_EOL;
            $gameService->updateFields();
            echo 'Collecting ready products...' . PHP_EOL;
            $gameService->collectReady();
            echo 'Updating stock...' . PHP_EOL;
            $gameService->updateStock();
            echo 'Seeding products...' . PHP_EOL;
            $gameService->seed();
        }
    }
}
