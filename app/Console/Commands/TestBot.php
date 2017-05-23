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
        $players = Player::all();

        foreach ($players as $player){
            $gameService = new GameService($player);

            $gameService->update();
        }
    }

    private function seedCarrot($position)
    {
        $url = 'http://s15.wolnifarmerzy.pl/ajax/farm.php?rid=' . $this->token . '&mode=garden_plant&farm=1&position=1&pflanze[]=17&feld[]=' . $position . '&felder[]=' . $position . '&cid=15';
        return $this->client->request('GET', $url);
    }

    private function collect($position)
    {
        $url = 'http://s15.wolnifarmerzy.pl/ajax/farm.php?rid='.$this->token.'&mode=garden_harvest&farm=1&position=1&pflanze[]=17&feld[]='.$position.'&felder[]='.$position;
        return $this->client->request('GET', $url);
    }
}
