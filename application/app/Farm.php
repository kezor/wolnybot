<?php

namespace App;


use App\Building\AbstractBuilding;
use App\Building\Farmland;

class Farm
{
    /**
     * @var Farmland[]
     */
    private $farmlands = [];

    /**
     * @var AbstractBuilding[]
     */
    private $buildings = [];

    public function addFarmland(Farmland $farmland)
    {
        $this->farmlands[] = $farmland;
        return $this;
    }

    public function addBuilding(AbstractBuilding $building)
    {
        $this->buildings[] = $building;
        return $this;
    }

    public function process()
    {
        $this->processFarmlands();
        $this->processBuildings();
    }

    private function processFarmlands()
    {
        foreach ($this->farmlands as $farmland){
            $farmland->process();
        }
    }

    private function processBuildings()
    {
        foreach ($this->buildings as $building){
            $building->process();
        }
    }
}