<?php

namespace App;

use App\Models\BaseModel;

/**
 * Class Player
 * @package App
 * @property integer id
 * @property boolean active
 * @property string username
 * @property string password
 * @property integer server_id
 * @property integer user_id
 * @property Task[] tasks
 */
class Player extends BaseModel
{

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function farms()
    {
        return $this->hasMany(Farm::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany|Task[]
     */
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
