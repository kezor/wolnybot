<?php

namespace App;


use App\Product\AbstractProduct;

class ProductFactory
{

    public static function getProductFromField(Field $field)
    {
        return self::getProductFromPid($field->product_pid, $field);
    }

    public static function getProductFromPid($pid, Field $field = null)
    {
        $productName = ProductMapper::getProductNameByPid($pid);

        $productClassName = 'App\\Product\\' . $productName;

        if (class_exists($productClassName)) {
            /** @var AbstractProduct $product */
            $product = new $productClassName($field);
            $product->setPid($pid);
            return $product;
        }
        throw new \Exception('Class "' . $productClassName . '" not found');
    }

}