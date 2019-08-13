<?php

namespace App\Repository;

use App\Building\Farmland;
use App\Field;
use App\Player;
use App\Product;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FieldRepository
{
    use DatabaseTransactions;

    public static function getField($index, Farmland $farmland)
    {
        $field = Field::where('space_id', $farmland->id)
            ->where('index', $index)
            ->first();
        if (!$field) {
            $field = new Field();
            $field->index = $index;
            $field->space_id = $farmland->id;
            $field->save();
        }
        return $field;
    }
}