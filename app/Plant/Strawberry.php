<?php
/**
 * Created by PhpStorm.
 * User: maciej
 * Date: 23.05.17
 * Time: 21:16
 */

namespace App\Plant;


class Strawberry extends AbstractPlant
{
    protected $length = 1;

    protected $height = 1;

    protected $name = 'Truskawka';

    public function getType()
    {
        return self::PLANT_TYPE_STRAWBERRY;
    }
}