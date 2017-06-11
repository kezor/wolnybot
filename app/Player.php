<?php

namespace App;

use App\Repository\SpaceRepository;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Player
 * @package App
 * @property integer id
 * @property boolean active
 * @property string username
 * @property string password
 * @property integer server_id
 */
class Player extends Model
{
    public function getSpaces()
    {
        return SpaceRepository::getPlayerSpaces($this);
    }
}
