<?php

namespace App\Repository;

use App\Player;
use App\Task;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Collection;

class TaskRepository
{
    use DatabaseTransactions;

    public static function getPlayerTaskReadyToRun(Player $player)
    {
        return Task::where('player_id', $player->id)
            ->whereNull('nextrun')
            ->get();
    }
}