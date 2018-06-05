<?php

namespace App\Repository;

use App\Building\Farmland;
use App\Farm;
use App\Field;
use App\Player;
use App\Product;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FieldRepository
{
    use DatabaseTransactions;

    public static function getField($index, Farmland $farmland)
    {
        $farm = Field::where('space_id', $farmland->getPosition())
            ->where('index', $index)
            ->first();
        if (!$farm) {
            $farm = new Field();
            $farm->index = $index;
            $farm->space_id = $farmland->getPosition();
        }
        return $farm;
    }

    public static function getEmptyItems($ids, Player $player)
    {
        return Product::whereNotIn('id', $ids)
            ->where('player_id', $player->id)
            ->get();
    }
}