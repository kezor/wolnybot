<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Space
 * @package App
 * @property integer space
 * @property integer index
 * @property integer offset_x
 * @property integer offset_y
 * @property integer phase
 * @property mixed   planted
 * @property mixed   time
 * @property integer product_pid
 * @property integer space_id
 * @property bool    water
 */
class Field extends Model
{
    private $product;

    protected $fillable = [
        'index',
        'phase',
        'product_pid',
        'time',
        'offset_x',
        'offset_y',
        'water'
    ];

    public function __construct()
    {
        $this->index       = null;
        $this->phase       = Product::PLANT_PHASE_EMPTY;
        $this->product_pid = null;
        $this->time        = 0;
        $this->product     = null;
        $this->offset_x    = 99;
        $this->offset_y    = 99;
        $this->water       = false;
    }

    public function isReadyToCrop()
    {
        return $this->phase == Product::PLANT_PHASE_FINAL
            && $this->getProduct()
            && $this->isVegetable();
    }

    public function canSeed()
    {
        return $this->phase == Product::PLANT_PHASE_EMPTY
            && $this->product_pid === null
            && $this->time == 0;
    }

    public function canWater()
    {
        return $this->phase != Product::PLANT_PHASE_EMPTY
            && !$this->isWatered()
            && $this->time != 0
            && $this->isVegetable();
    }

    public function isWatered()
    {
        return (bool)$this->water;
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

    public function getStatusIcon()
    {
        if ($this->getProduct() && $this->getProduct()->isPlant()) {
            if ($this->phase === Product::PLANT_PHASE_FINAL) {
                return '<span class="badge badge-success">&nbsp;&nbsp;</span>';
            } else {
                return '<span class="badge badge-warning">&nbsp;&nbsp;</span>';
            }
        }

        if ($this->phase === Product::PLANT_PHASE_FINAL) {
            return '<span class="badge badge-dark">&nbsp;&nbsp;</span>';
        }

        return '<span class="badge badge-light">&nbsp;&nbsp;</span>';
    }
}
