<?php

namespace App\Repository;

use App\Field;
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

    public static function getProductByPid(Field $field)
    {
        $product = Product::where('pid', $field->product_pid)
            ->where('player', $field->getSpace()->getPlayer()->id)
            ->first();
        if (!$product) {
            $product = new Product();
            $product->pid = $field->product_pid;
            $product->player = $field->getSpace()->getPlayer()->id;
            $product->amount = 0;
            $product->save();
        }
        return $product;
    }
}