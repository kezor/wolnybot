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
 * @property mixed planted
 * @property mixed time
 */
class Field extends Model
{
    public function canCollect()
    {
        return $this->phase == Product::PLANT_PHASE_FINAL
            && $this->isVegetable();
    }

    /**
     * @var Product
     */
    private $product = null;

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
        $this->product = $product;
        return $this;
    }

    /**
     * @return Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    public function getFields()
    {
        if(!$this->product){
            throw new \Exception('Field doesn\'t have product');
        }
        $fields = [];

        for ($i = 0; $i < $this->product->getLength(); $i++) {
            for ($j = 0; $j < $this->product->getHeight(); $j++) {
                $fields[] = $this->index + $i + (12 * $j);
            }
        }

        return implode(',', $fields);
    }

    public function isVegetable()
    {
        return in_array($this->product_pid, ProductCategoryMapper::getVegetablesPids());
    }

    public function setAsEmpty()
    {
        $this->product_pid = null;
        $this->time = 0;
        $this->planted = 0;
        $this->save();
        return $this;
    }

    public function getSpace()
    {
        return SpaceRepository::getById($this->space);
    }
}
