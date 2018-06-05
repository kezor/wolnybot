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
 * @property integer user_id
 */
class Player extends Model
{

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function farms()
    {
        return $this->hasMany(Farm::class);
    }
}
