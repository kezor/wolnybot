<?php

namespace App;

use App\Repository\SpaceRepository;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Space
 * @package App
 * @property integer space
 * @property integer player
 * @property integer farm
 * @property integer position
 * @property bool fields_in_database
 * @property integer building_type
 */
class Space extends Model
{

}
