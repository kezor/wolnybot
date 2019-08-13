<?php

namespace App\Console\Commands;

use App\Player;
use App\Service\GameService;
use Illuminate\Console\Command;

class DisableTutorial extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'player:disableTutorial {userId}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Disable tutorial for user';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $userId = $this->argument('userId');

        $player = Player::find($userId);

        if (!$player) {
            $this->output->writeln('Player with Id :'.$userId);
            return;
        }

        $game = new GameService($player);
        $game->disableTutorial();
    }
}
