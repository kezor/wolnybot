<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Space
 * @package App
 * @property integer id
 * @property integer player
 * @property integer farm
 * @property integer position
 * @property boolean fields_in_database
 */
class Space extends Model
{

    public function isFieldsInDatabase(){
        return $this->fields_in_database;
    }
}
