<?php

namespace App\Repository;

use App\Farm;
use App\Player;
use App\Product;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FarmRepository
{
    use DatabaseTransactions;

    public static function getFarm($farmId, Player $player)
    {
        $farm = Farm::where('farm_id', $farmId)
            ->where('player_id', $player->id)
            ->first();
        if (!$farm) {
            $farm = new Farm();
            $farm->farm_id = $farmId;
            $farm->player_id = $player->id;
        }
        return $farm;
    }

    public static function getEmptyItems($ids, Player $player)
    {
        return Product::whereNotIn('id', $ids)
            ->where('player', $player->id)
            ->get();
    }
}