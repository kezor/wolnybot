<?php

namespace App\Repository;

use App\Player;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Collection;

class PlayerRepository
{
    use DatabaseTransactions;

    /**
     * @return Collection|Player[]
     */
    public static function getAllActive()
    {
        return Player::where('active', true)
            ->get();
    }
}