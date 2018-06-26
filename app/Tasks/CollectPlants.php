<?php

namespace App\Tasks;


use App\Building\Farmland;
use App\Product;

class CollectPlants
{
    /**
     * @var Product
     */
    public $productToSeed;

    /**
     * @var Farmland
     */
    public $farmland;

    /**
     * @var int
     */
    public $goal;

//    public function __construct($data = [])
//    {
//        if (!empty($data)) {
//            $this->productToSeed = Product::find()
//        }
//    }

//    public function toJson($options = 0)
//    {
//        return [
//            'product_to_seed_pid' => $this->productToSeed->pid,
//            'farm_id' => $this->farmland->farm_id,
//            'position' => $this->farmland->position
//        ];
//    }
}