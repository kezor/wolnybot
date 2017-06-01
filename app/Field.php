<?php

namespace App;

use App\Product\AbstractProduct;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Space
 * @package App
 * @property integer space
 * @property integer index
 * @property integer product_pid
 * @property integer offset_x
 * @property integer offset_y
 * @property integer phase
 * @property mixed planted
 * @property mixed time
 */
class Field extends Model
{
    public function canCollect()
    {
        return $this->phase == AbstractProduct::PLANT_PHASE_FINAL
            && $this->isVegetable();
    }

    public function drawField()
    {
        $char = $this->product_pid;
        if (strlen($char) == 1) {
            $char = ' ' . $char;
        }
        return '[' . $char . ']';
    }

    public function isVegetable()
    {
        return in_array($this->product_pid, ProductCategoryMapper::getVegetablesPids());
    }
}
