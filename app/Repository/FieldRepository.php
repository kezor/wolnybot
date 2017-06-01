<?php

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
            $field->space = $space->id;
            $field->index = $fieldData['teil_nr'];
        }
        return $field;
    }
}