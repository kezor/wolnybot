<?php

namespace App\Product;


class Carrot extends AbstractProduct
{
    protected $length = 1;

    protected $height =1;

    protected $name = 'Marchew';

    public function getType()
    {
        return self::PLANT_TYPE_CARROT;
    }

}