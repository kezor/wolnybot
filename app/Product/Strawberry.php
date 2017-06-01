<?php

namespace App\Product;


class Strawberry extends AbstractProduct
{
    protected $length = 1;

    protected $height = 1;

    protected $name = 'Truskawka';

    public function getType()
    {
        return self::PLANT_TYPE_STRAWBERRY;
    }
}