<?php

namespace App;


use App\Product\AbstractProduct;

class ProductFactory
{

    public static function getProductFromField(Field $field)
    {
        $productName = ProductMapper::getProductNameByPid($field->product_pid);

        $productClassName = 'App\\Product\\' . $productName;

        if (class_exists($productClassName)) {
            /** @var AbstractProduct $product */
            return new $productClassName($field);
        }
        throw new \Exception('Class "' . $productClassName . '" not found');
    }

}