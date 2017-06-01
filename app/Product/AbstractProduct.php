<?php

namespace App\Product;


use App\Field;

abstract class AbstractProduct
{
    protected $length = null;
    protected $height = null;

    protected $name = null;

    protected $amount = null;

    protected $size = 1;

    const PLANT_PHASE_FINAL = 4;

    /**
     * @var Field
     */
    protected $field;

    public function __construct(Field $field = null)
    {
        $this->field = $field;
    }

    public function getPid()
    {
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

    public function decreaseAmount()
    {
        $this->amount--;
    }

    public function canCollect()
    {
        return $this->field->canCollect();
    }

    public function setAsEmpty()
    {
        $this->field->product_pid = null;
        $this->field->time = 0;
        $this->field->planted = 0;
        $this->field->save();
    }

    abstract public function getType();

    public function getIndex()
    {
        return $this->field->index;
    }

    public function getFields()
    {
        $fields = [];

        for ($i = 0; $i < $this->getLength(); $i++) {
            for ($j = 0; $j < $this->getHeight(); $j++) {
                $fields[] = $this->getIndex() + $i + (12 * $j);
            }
        }

        return implode(',', $fields);
    }

    public function getName()
    {
        return $this->name;
    }
}