<?php
/**
 * Created by PhpStorm.
 * User: maciej
 * Date: 23.05.17
 * Time: 21:16
 */

namespace App\Plant;


class Cucumber extends AbstractPlant
{
    protected $length = 1;

    protected $height = 1;

    protected $name = 'Ogórek';

    public function getType()
    {
        return self::PLANT_TYPE_CUCUMBER;
    }
}