<?php

namespace App;

use App\Models\BaseModel;

/**
 * Class Space
 * @package App
 * @property integer id
 * @property integer player_id
 * @property integer farm_id
 * @property integer position
 * @property bool fields_in_database
 * @property integer building_type
 * @property integer remain
 */
class Space extends BaseModel
{

    public const TYPE_FARMLAND = 1;
    public const TYPE_HOVEL = 2;


    protected $fillable = [
        'player','farm', 'position', 'building_type'
    ];

    public function getBuildingTypeName()
    {
        return SpaceMapper::getSpaceNameByPid($this->building_type);
    }

    public function getPosition()
    {
        return $this->position;
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'space_id');
    }
}
