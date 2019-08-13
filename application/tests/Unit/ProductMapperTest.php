<?php

namespace Tests\Unit;


use App\ProductMapper;
use Tests\TestCase;

class ProductMapperTest extends TestCase
{
    public function testGetProductNameByPid()
    {
        $this->assertEquals('Carrot', ProductMapper::getProductNameByPid(17));
    }

    public function testGetNotExistProductName()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Product id: 999999999 not found');

        ProductMapper::getProductNameByPid(999999999);
    }
}