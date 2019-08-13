<?php

namespace App\Console\Commands;

use App\Connector\WolniFarmerzyConnector;
use App\Player;
use App\Service\GameService;
use App\Service\HovelService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

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
        /** @var Collection $players */
        if ($this->option('username')) {
            $players = Player::where('username', $this->option('username'))->get();
        } else {
            $players = Player::where('active', true)->get();
        }

        if ($players->isEmpty()) {
            $this->info('Not active users found');

            return 0;
        }

        $minutes = mt_rand(3, 23);
        Log::debug('Sleeping for '.$minutes. 'minutest before run script...');
        sleep($minutes * 60);

        Log::debug('Starting game process...');

        foreach ($players as $player) {
            Log::debug('Starting working with player "' . $player->username . '" on server id: ' . $player->server_id . '  ');
            $gameService = new GameService($player);
            $gameService->run();
            Log::debug('Ended working with player "' . $player->username . '" on server id: ' . $player->server_id . '  ');
        }
    }
}
