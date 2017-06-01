<?php

namespace App\Product;


class Cucumber extends AbstractProduct
{
    protected $length = 1;

    protected $height = 1;

    protected $name = 'Ogórek';

    public function getType()
    {
        return self::PLANT_TYPE_CUCUMBER;
    }
}