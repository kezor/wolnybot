<?php
/**
 * Created by PhpStorm.
 * User: maciej
 * Date: 23.05.17
 * Time: 21:16
 */

namespace App\Plant;


class FakeBigPlant5x3 extends AbstractPlant
{
    protected $length = 5;

    protected $height = 3;

    protected $name = 'BigFakePlant';

    public function getType()
    {
        return self::PLANT_TYPE_FAKE_PLANT;
    }
}