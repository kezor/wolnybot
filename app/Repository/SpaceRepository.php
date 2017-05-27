<?php
/**
 * Created by PhpStorm.
 * User: maciej
 * Date: 24.05.17
 * Time: 13:26
 */

namespace App\Repository;


use App\Player;
use App\Space;

class SpaceRepository
{

    public function getSpace($spaceData, $player)
    {
        $space = Space::where('player', $player->id)
            ->where('farm', $spaceData['farm'])
            ->where('position', $spaceData['position'])
            ->first();
        if (!$space) {
            $space = new Space();
            $space->player   = $player->id;
        }

        return $space;
    }

    public function getPlayerSpaces(Player $player){
        return Space::where('player', $player->id)->get();
    }
}