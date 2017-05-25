<?php
/**
 * Created by PhpStorm.
 * User: maciej
 * Date: 24.05.17
 * Time: 13:26
 */

namespace App\Repository;

use App\Player;
use App\Stock;

class StockRepository
{
    public function getStock($stockData, Player $player)
    {
        $stock = Stock::where('plant_pid', $stockData['pid'])
            ->where('player', $player->id)
            ->first();
        if (!$stock) {
            $stock            = new Stock();
            $stock->plant_pid = $stockData['pid'];
            $stock->player = $player->id;
        }
        return $stock;
    }
}