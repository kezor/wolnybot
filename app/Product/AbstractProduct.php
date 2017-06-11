<?php

namespace App\Product;


use App\Field;

abstract class AbstractProduct
{
    protected $length = null;
    protected $height = null;

    protected $name = null;

    protected $amount = null;

    protected $pid = null;

    protected $size = 1;

    const PLANT_PHASE_FINAL = 4;

    public function setPid($pid)
    {
        $this->pid = $pid;
        return $this;
    }

    public function getPid()
    {
        if ($this->pid) {
            return $this->pid;
        }
        return $this->field->product_pid;
    }

    public function getLength()
    {
        return ($this->length) ? $this->length : $this->size;
    }

    public function getHeight()
    {
        return ($this->height) ? $this->height : $this->size / 2;
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

    public function canCollect()
    {
        return $this->field->canCollect();
    }

    public function getIndex()
    {
        return $this->field->index;
    }

    public function getName()
    {
        return $this->name;
    }
}