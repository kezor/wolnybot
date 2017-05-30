<?php
/**
 * Created by PhpStorm.
 * User: maciej
 * Date: 23.05.17
 * Time: 21:17
 */

namespace App\Plant;


use App\Field;

abstract class AbstractPlant
{
    protected $length = null;
    protected $height = null;

    protected $name = 'BRAK';

    protected $amount = null;

    const PLANT_TYPE_WHEAT = 1;
    const PLANT_TYPE_CARROT = 17;
    const PLANT_TYPE_CUCUMBER = 18;
    const PLANT_TYPE_STRAWBERRY = 20;

    const PLANT_TYPE_CORN = 88;
    const PLANT_TYPE_FAKE_PLANT = 77;

    /**
     * @var Field
     */
    protected $field;

    public function __construct(Field $field = null)
    {
        $this->field = $field;
    }

    public function getLength()
    {
        return $this->length;
    }

    public function getHeight()
    {
        return $this->height;
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
        $this->field->plant_type = Field::FIELD_EMPTY;
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