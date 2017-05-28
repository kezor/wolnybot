<?php
/**
 * Created by PhpStorm.
 * User: maciej
 * Date: 23.05.17
 * Time: 21:16
 */

namespace App\Plant;


class Corn extends AbstractPlant
{
    protected $length = 2;

    protected $height = 2;

    protected $name = 'Kukurydza';

    public function getType()
    {
        return self::PLANT_TYPE_CORN;
    }
}