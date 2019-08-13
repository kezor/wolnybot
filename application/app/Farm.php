<?php

namespace App;


use App\Building\Farmland;
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
        /** @var Space $space */
        foreach ($this->spaces as $space) {
            if ($space->position === $position) {
                if($space->building_type == Space::TYPE_FARMLAND){
                    return $this->objectToObject($space, Farmland::class);
                }
                return $space;
            }
        }
        return null;
    }

    private function objectToObject($instance, $className) {
        return unserialize(sprintf(
            'O:%d:"%s"%s',
            strlen($className),
            $className,
            strstr(strstr(serialize($instance), '"'), ':')
        ));
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