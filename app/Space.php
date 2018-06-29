<?php

namespace App;

use App\Repository\SpaceRepository;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Space
 * @package App
 * @property integer player_id
 * @property integer farm_id
 * @property integer position
 * @property bool fields_in_database
 * @property integer building_type
 * @property integer remain
 */
class Space extends Model
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
        return $this->hasMany(Task::class);
    }
}
