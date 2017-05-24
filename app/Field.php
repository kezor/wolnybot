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
}
