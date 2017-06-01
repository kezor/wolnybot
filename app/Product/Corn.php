<?php

namespace App\Product;


class Corn extends AbstractProduct
{
    protected $length = 2;

    protected $height = 2;

    protected $name = 'Kukurydza';

    public function getType()
    {
        return self::PLANT_TYPE_CORN;
    }
}