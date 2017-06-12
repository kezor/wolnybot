<?php

namespace App\Repository;

use App\Player;

class PlayerRepository
{
    public static function getById($id)
    {
        return Player::where('id', $id)->first();
    }
}