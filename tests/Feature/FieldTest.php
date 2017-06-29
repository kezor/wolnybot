<?php

namespace Tests\Feature;


use App\Product;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class FieldTest extends TestCase
{
    use DatabaseTransactions;

    public function testCanCollect()
    {
        $fieldReadyToCollect = $this->getField();

        $this->assertNotNull($fieldReadyToCollect->getProduct());
        $this->assertTrue($fieldReadyToCollect->canCollect());

        $fieldNotReadyToCollect = $this->getField(Product::PLANT_PHASE_BEGIN);

        $this->assertNotNull($fieldReadyToCollect->getProduct());
        $this->assertFalse($fieldNotReadyToCollect->canCollect());
    }

    public function testSetAsEmpty()
    {
        $field = $this->getField();

        $this->assertTrue($field->canCollect());

        $field->removeProduct();

        $this->assertFalse($field->canCollect());
        $this->assertNull($field->getProduct());
        $this->assertEquals(Product::PLANT_PHASE_EMPTY, $field->phase);
        $this->assertEquals(null, $field->product_pid);
        $this->assertEquals(0, $field->time);
        $this->assertEquals(0, $field->planted);
    }

    public function testGetRelatedFields()
    {
        $field = $this->getField();

        $this->assertTrue($field->canCollect());

        $field->removeProduct();

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Field doesn\'t have product');
        $field->getRelatedFields();
    }

    public function testGetSpace()
    {
        $field = $this->getField();

        $this->assertNotNull($field->getSpace());
    }

    private function getField($phase = Product::PLANT_PHASE_FINAL)
    {
        $player = $this->getTestPlayer();
        $product = $this->getTestProduct($player, 17); // carrot
        $space = $this->getTestSpace($player);
        return $this->getTestField($product, $space, $phase);
    }
}
