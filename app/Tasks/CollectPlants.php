<?php

namespace App\Tasks;


use App\Building\Farmland;
use App\Product;

class CollectPlants extends AbstractTask
{
    public const NAME = 'Collect Plants';

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

}