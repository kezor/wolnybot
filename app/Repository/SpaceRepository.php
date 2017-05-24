<?php
/**
 * Created by PhpStorm.
 * User: maciej
 * Date: 24.05.17
 * Time: 13:26
 */

namespace App\Repository;


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
            $space->player   = $this->player->id;
        }

        return $space;
    }
}