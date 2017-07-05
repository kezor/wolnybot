<?php

namespace Tests\Unit;

use App\ProductDurationService;
use Tests\TestCase;

class ProductDurationServiceTest extends TestCase
{

    public function testGetProductDuration()
    {
        $this->assertEquals(900, ProductDurationService::getProductDurationByPid(17)); // Carrot
        $this->assertEquals(2700, ProductDurationService::getProductDurationByPid(2)); // Wheat
        $this->assertEquals(372000, ProductDurationService::getProductDurationByPid(42)); // ?
        $this->assertEquals(0, ProductDurationService::getProductDurationByPid(66)); // ?
        $this->assertEquals(0, ProductDurationService::getProductDurationByPid(99999999)); // ?
    }
}
