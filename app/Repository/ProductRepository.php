<?php
/**
 * Created by PhpStorm.
 * User: maciej
 * Date: 24.05.17
 * Time: 13:26
 */

namespace App\Repository;

use App\Player;
use App\Product;

class ProductRepository
{
    public static function getStock($stockData, Player $player)
    {
        $stock = Product::where('pid', $stockData['pid'])
            ->where('player', $player->id)
            ->first();
        if (!$stock) {
            $stock = new Product();
            $stock->pid = $stockData['pid'];
            $stock->player = $player->id;
        }
        return $stock;
    }

    public static function getEmptyItems($ids, Player $player)
    {
        return Product::whereNotIn('id', $ids)
            ->where('player', $player->id)
            ->get();
    }
}