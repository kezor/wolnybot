<?php

namespace App;


use Illuminate\Database\Eloquent\Model;


/**
 * Class Farm
 * @package App
 * @property int $id
 * @property int $farm_id
 * @property int $player_id
 *
 */
class Farm extends Model
{
    public function spaces()
    {
        return $this->hasMany(Space::class);
    }

    public function getSpace($position)
    {
        foreach ($this->spaces as $space) {
            if ($space->position === $position) {
                return $space;
            }
        }
        return null;
    }

    public function hasSpaceAt($positon)
    {
        return (bool)$this->getSpace($positon);
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

    public function player()
    {
        return $this->belongsTo(Player::class);
    }

    public function getPlayer()
    {
        return $this->player;
    }
}