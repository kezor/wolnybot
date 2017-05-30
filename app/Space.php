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
 * @property integer building_type
 * @property boolean fields_in_database
 */
class Space extends Model
{

    public function isFieldsInDatabase()
    {
        return $this->fields_in_database;
    }

    public function getFields()
    {
        return Field::where('space', $this->id)->get();
    }

    public function getFieldsToCollect()
    {
        return Field::where('phase', 4)
            ->where('space', $this->id)
            ->where('time', '!=', 0)
            ->get();
    }
}
