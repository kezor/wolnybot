<?php

namespace App;

use App\Repository\SpaceRepository;
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
 * @property mixed   planted
 * @property mixed   time
 */
class Field
{
    private $phase;

    private $time;

    private $offset_x;

    private $offset_y;

    private $index;

    private $product_pid;

    private $product;

    private $water;

    public function __construct($index)
    {
        $this->index       = $index;
        $this->phase       = Product::PLANT_PHASE_EMPTY;
        $this->product_pid = null;
        $this->time        = 0;
        $this->product     = null;
    }

    public function canCollect()
    {
        return $this->phase == Product::PLANT_PHASE_FINAL
            && $this->getProduct()
            && $this->isVegetable();
    }

    public function canSeed()
    {
        return $this->phase == Product::PLANT_PHASE_EMPTY
            && $this->getProductPid() === null
            && $this->time == 0;
    }

    public function canWater()
    {
        return $this->phase != Product::PLANT_PHASE_EMPTY
            && !$this->isWatered()
            && $this->time != 0
            && $this->isVegetable();
    }

    /**
     * @param bool $iswater
     * @return $this
     */
    public function setWater($iswater)
    {
        $this->water = $iswater;

        return $this;
    }

    public function isWatered()
    {
        return $this->water;
    }

    public function drawField()
    {
        $char = $this->product_pid;
        if (strlen($char) == 1) {
            $char = ' ' . $char;
        }

        return '[' . $char . ']';
    }

    public function setProduct(Product $product)
    {
        $this->product     = $product;
        $this->product_pid = $product->pid;

        return $this;
    }

    public function removeProduct()
    {
        $this->product_pid = null;
        $this->time        = 0;
        $this->planted     = 0;
        $this->phase       = Product::PLANT_PHASE_EMPTY;
        $this->product     = null;

        return $this;
    }

    /**
     * @return Product
     */
    public function getProduct()
    {
        if (!$this->product && $this->getProductPid()) {
            $product = new Product();
            $product->setPid($this->getProductPid());
            $this->setProduct($product);
        }

        return $this->product;
    }

    public function getRelatedFields()
    {
        $fields = [];

        if (!$this->getProduct()) {
            return false;
        }
        for ($i = 0; $i < $this->getProduct()->getLength(); $i++) {
            for ($j = 0; $j < $this->getProduct()->getHeight(); $j++) {
                $fields[] = $this->index + $i + (12 * $j);
            }
        }

        return implode(',', $fields);

    }

    public function isVegetable()
    {
        return in_array($this->getProductPid(), ProductCategoryMapper::getVegetablesPids());
    }

    public function setTime($time)
    {
        $this->time = $time;

        return $this;
    }

    public function getTime()
    {
        return $this->time;
    }

    public function setPlanted($planted)
    {
        $this->planted = $planted;

        return $this;
    }

    public function getPlanted()
    {
        return $this->planted;
    }

    public function setOffsetX($offset)
    {
        $this->offset_x = $offset;

        return $this;
    }

    public function setOffsetY($offset)
    {
        $this->offset_y = $offset;

        return $this;
    }

    public function getOffsetX()
    {
        return $this->offset_x;
    }

    public function getOffsetY()
    {
        return $this->offset_y;
    }

    public function setPhase($phase)
    {
        $this->phase = $phase;

        return $this;
    }

    public function getPhase()
    {
        return $this->phase;
    }

    public function setProductPid($pid)
    {
        $this->product_pid = $pid;

        return $this;
    }

    public function getProductPid()
    {
        return $this->product_pid;
    }

    public function getIndex()
    {
        return $this->index;
    }
}
