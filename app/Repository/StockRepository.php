<?php
/**
 * Created by PhpStorm.
 * User: maciej
 * Date: 24.05.17
 * Time: 13:26
 */

namespace App\Repository;

use App\Stock;

class StockRepository
{
    public function getStock($stockData)
    {
        $stock = Stock::where('plant_pid', $stockData['pid'])
            ->first();
        if (!$stock) {
            $stock            = new Stock();
            $stock->plant_pid = $stockData['pid'];
        }
        return $stock;
    }
}