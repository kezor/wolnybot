<?php
/**
 * Created by PhpStorm.
 * User: maciej
 * Date: 26.05.17
 * Time: 14:56
 */

namespace App\Building;


use App\BuildingType;

class Farmland
{
    public function getType(){
        return BuildingType::FARMLAND;
    }
}