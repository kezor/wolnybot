<?php

namespace App\Repository;

use App\Player;
use App\Task;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Collection;

class TaskRepository
{
    use DatabaseTransactions;

    /**
     * @param Player $player
     * @return Collection|Task[]
     */
    public static function getPlayerTaskReadyToRun(Player $player)
    {
        return Task::where('player_id', $player->id)
            ->where('status', Task::TASK_STATUS_ACTIVE)
            ->whereNull('nextrun')
            ->get();
    }
}