<?php

namespace App;


use Illuminate\Database\Eloquent\Model;

class Farm extends Model
{
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

    public function player()
    {
        return $this->belongsTo(Player::class);
    }

    public function getPlayer()
    {
        return $this->player;
    }
}