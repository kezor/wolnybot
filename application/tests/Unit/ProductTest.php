<?php

namespace Tests\Unit;

use App\Product;
use Tests\TestCase;

class ProductTest extends TestCase
{
    public function testAmount()
    {
        $product = new Product();

        $product->setAmount(345);

        $this->assertEquals(345, $product->getAmount());
        $this->assertInstanceOf(Product::class, $product->decreaseAmount());
        $this->assertEquals(344, $product->getAmount());
    }
}
