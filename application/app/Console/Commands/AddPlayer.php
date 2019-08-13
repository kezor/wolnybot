<?php

namespace App\Console\Commands;

use App\Player;
use Illuminate\Console\Command;

class AddPlayer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'player:add {username} {password} {serverId}';

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
        $player = new Player();
        $player->username = $this->argument('username');
        $player->password = $this->argument('password');
        $player->server_id = $this->argument('serverId');
        $player->active = true;
        $player->save();
    }
}
