<?php

namespace App\Product;


class Wheat extends AbstractProduct
{
    protected $length = 2;

    protected $height = 1;

    protected $name = 'Pszenica';

    public function getType()
    {
        return self::PLANT_TYPE_WHEAT;
    }
}