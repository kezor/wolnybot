<?php
/**
 * Created by PhpStorm.
 * User: maciej
 * Date: 23.05.17
 * Time: 21:16
 */

namespace App\Plant;


class Wheat extends AbstractPlant
{
    protected $length = 2;

    protected $height = 1;

    protected $name = 'Pszenica';

    public function getType()
    {
        return self::PLANT_TYPE_WHEAT;
    }
}