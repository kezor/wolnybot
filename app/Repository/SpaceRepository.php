<?php

namespace App\Repository;

use App\Farm;
use App\Player;
use App\Product;
use App\Space;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SpaceRepository
{
    use DatabaseTransactions;

    public static function getSpace(Farm $farm, Player $player, $spaceData)
    {
        $space = Space::where('farm_id', $farm->id)
            ->where('player_id', $player->id)
            ->where('position', $spaceData['position'])
            ->first();
        if (!$space) {
            $space = new Space();
            $space->farm_id = $farm->id;
            $space->player_id = $player->id;
            $space->position = $spaceData['position'];
        }
        $space->building_type = $spaceData['buildingid'];
        return $space;
    }

    public static function getEmptyItems($ids, Player $player)
    {
        return Product::whereNotIn('id', $ids)
            ->where('player', $player->id)
            ->get();
    }
}