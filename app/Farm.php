<?php

namespace App;


use App\Building\AbstractBuilding;
use App\Building\Farmland;
use Illuminate\Database\Eloquent\Model;

class Farm extends Model
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
        foreach ($this->farmlands as $farmland) {
            $farmland->process();
        }
    }

    private function processBuildings()
    {
        foreach ($this->buildings as $building) {
            $building->process();
        }
    }

    public function spaces()
    {
        return $this->hasMany(Space::class);
    }

    public function getSpaceNameAtPosition($position)
    {
        /** @var Space $space */
        foreach ($this->spaces as $space) {
            if ($space->position === $position) {
                return SpaceMapper::getSpaceNameByPid($space->building_type);
            }
        }

        return 'Not in use';
    }

    public function getId()
    {
        return $this->id;
    }

    public function player()
    {
        return $this->belongsTo(Player::class);
    }

    public function getPlayer()
    {
        return $this->player;
    }
}