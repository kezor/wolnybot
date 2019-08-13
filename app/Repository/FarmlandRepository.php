<?php

namespace App\Repository;

use App\Building\Farmland;
use App\Farm;
use App\Player;
use App\Product;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FarmlandRepository
{
    use DatabaseTransactions;

    public static function getFarmland(Farm $farm, Player $player, $spaceData)
    {
        $farmland = Farmland::where('farm_id', $farm->id)
            ->where('player_id', $player->id)
            ->where('position', $spaceData['position'])
            ->first();
        if (!$farmland) {
            $farmland = new Farmland();
            $farmland->farm_id = $farm->id;
            $farmland->player_id = $player->id;
            $farmland->position = $spaceData['position'];
        }
        $farmland->building_type = $spaceData['buildingid'];
        $farmland->save();
        return $farmland;
    }

    public static function getEmptyItems($ids, Player $player)
    {
        return Product::whereNotIn('id', $ids)
            ->where('player', $player->id)
            ->get();
    }
}