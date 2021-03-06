<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Space
 * @package App
 * @property integer id
 * @property integer player_id
 * @property integer pid
 * @property integer amount
 */
class Product extends Model
{
    const PLANT_PHASE_FINAL = 4;
    const PLANT_PHASE_BEGIN = 1;
    const PLANT_PHASE_EMPTY = 0;

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
        return ProductSizeService::getProductHeightByPid($this->pid);
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
        return $this;
    }

    public function getName()
    {
        return ProductMapper::getProductNameByPid($this->pid);
    }

    public function isPlant()
    {
        return in_array($this->getPid(), ProductCategoryMapper::getVegetablesPids());
    }
}
