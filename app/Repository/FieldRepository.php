<?php
/**
 * Created by PhpStorm.
 * User: maciej
 * Date: 24.05.17
 * Time: 13:26
 */

namespace App\Repository;


use App\Field;
use App\Space;

class FieldRepository
{
    public function getField($fieldData, Space $space)
    {
        $field = Field::where('space', $space->id)
            ->where('index', $fieldData['teil_nr'])
            ->first();
        if (!$field) {
            $field = new Field();
        }
        return $field;
    }
}