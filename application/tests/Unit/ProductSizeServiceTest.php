<?php

namespace Tests\Unit;


use App\ProductMapper;
use App\ProductSizeService;
use Tests\TestCase;

class ProductSizeServiceTest extends TestCase
{
    public function testGetProductSizeByPid()
    {
        $this->assertEquals(1, ProductSizeService::getProductSizeByPid(17)); // Carrot
        $this->assertEquals(2, ProductSizeService::getProductSizeByPid(1)); // Wheat
        $this->assertEquals(4, ProductSizeService::getProductSizeByPid(2)); // Corn
        $this->assertEquals(0, ProductSizeService::getProductSizeByPid(99999999)); // undefined
    }

    public function testGetProductLenght()
    {
        $this->assertEquals(1, ProductSizeService::getProductLenghtByPid(17)); // Carrot
        $this->assertEquals(2, ProductSizeService::getProductLenghtByPid(1)); // Wheat
        $this->assertEquals(2, ProductSizeService::getProductLenghtByPid(2)); // Corn
        $this->assertEquals(0, ProductSizeService::getProductLenghtByPid(9999999)); // undefined
    }

    public function testGetProductHeight()
    {
        $this->assertEquals(1, ProductSizeService::getProductHeightByPid(17)); // Carrot
        $this->assertEquals(1, ProductSizeService::getProductHeightByPid(1)); // Wheat
        $this->assertEquals(2, ProductSizeService::getProductHeightByPid(2)); // Corn
        $this->assertEquals(0, ProductSizeService::getProductHeightByPid(99999999)); // undefined
    }
}