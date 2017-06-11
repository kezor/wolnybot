<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Space
 * @package App
 * @property integer id
 * @property integer player
 * @property integer pid
 * @property integer amount
 * @property integer duration
 * @property integer size
 * @property integer product_type
 */
class Product extends Model
{
    const PLANT_PHASE_FINAL = 4;

    public function setPid($pid)
    {
        $this->pid = $pid;
        return $this;
    }

    public function getPid()
    {
        return $this->pid;
    }

    public function getLength()
    {
        if ($this->size > 2) {
            return $this->size / 2;
        }
        return $this->size;
    }

    public function getHeight()
    {
        if ($this->size > 1) {
            return $this->size / 2;
        }
        return $this->size;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    public function setSize($size)
    {
        $this->size = $size;
    }

    public function decreaseAmount()
    {
        $this->amount--;
    }
}
