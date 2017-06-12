<?php

namespace App\Repository;


use App\Player;
use App\Space;

class SpaceRepository
{
    public static function getById($id){
        return Space::where('id', $id)->first();
    }

    public static function getSpace($spaceData, $player)
    {
        $space = Space::where('player', $player->id)
            ->where('farm', $spaceData['farm'])
            ->where('position', $spaceData['position'])
            ->first();
        if (!$space) {
            $space = new Space();
            $space->player = $player->id;
            $space->farm = $spaceData['farm'];
            $space->position = $spaceData['position'];
        }

        return $space;
    }

    public static function getPlayerSpaces(Player $player)
    {
        return Space::where('player', $player->id)->get();
    }
}