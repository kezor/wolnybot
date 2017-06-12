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
        return ProductSizeService::getProductLenghtByPid($this->pid);
    }

    public function getHeight()
    {
        return ProductSizeService::getProductLenghtByPid($this->pid);
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    public function decreaseAmount()
    {
        $this->amount--;
    }
}
