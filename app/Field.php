<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Space
 * @package App
 * @property integer space
 * @property integer index
 * @property integer plant_type
 * @property integer offset_x
 * @property integer offset_y
 * @property integer phase
 * @property mixed planted
 * @property mixed time
 */
class Field extends Model
{
    const FIELD_EMPTY = 0;
    const FIELD_WEEDS = 13;
    const FIELD_STUMPS = 14;
    const FIELD_STONES = 15;
    const FIELD_COCKROACHES = 16;
    const FIELD_UNKNOWN = 99;

    public function canCollect()
    {
        return !in_array($this->plant_type, [
            self::FIELD_COCKROACHES,
            self::FIELD_WEEDS,
            self::FIELD_STONES,
            self::FIELD_STUMPS,
        ]) && $this->phase == 4;
    }

    public function drawField()
    {
        $char = $this->plant_type;
        if(strlen($char) == 1){
            $char = ' '.$char;
        }
        return '['.$char.']';
    }
}
