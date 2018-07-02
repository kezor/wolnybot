<?php

namespace App\Facades;


use App\Service\ActivitiesService as Activties;
use Illuminate\Support\Facades\Facade;

class ActivitiesService extends Facade
{
    protected static function getFacadeAccessor()
    {
        return Activties::class;
    }
}