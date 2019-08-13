<?php


namespace App\Tasks;


abstract class AbstractTask
{

    public function getName()
    {
        return CollectPlants::NAME;
    }
}