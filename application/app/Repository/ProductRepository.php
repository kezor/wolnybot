<?php

namespace App\Repository;

use App\Field;
use App\Player;
use App\Product;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ProductRepository
{
    use DatabaseTransactions;

    public static function getStock($stockData, Player $player)
    {
        $stock = Product::where('pid', $stockData['pid'])
            ->where('player_id', $player->id)
            ->first();
        if (!$stock) {
            $stock = new Product();
            $stock->pid = $stockData['pid'];
            $stock->player_id = $player->id;
        }
        return $stock;
    }

    public static function getEmptyItems($ids, Player $player)
    {
        return Product::whereNotIn('id', $ids)
            ->where('player_id', $player->id)
            ->get();
    }
}