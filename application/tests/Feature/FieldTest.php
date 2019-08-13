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
        $this->assertEquals(Product::PLANT_PHASE_EMPTY, $field->getPhase());
        $this->assertEquals(null, $field->getProductPid());
        $this->assertEquals(0, $field->getTime());
        $this->assertEquals(0, $field->getPlanted());
    }

    public function testGetRelatedFields()
    {
        $field = $this->getField();

        $this->assertTrue($field->canCollect());
        $field->removeProduct();
        $this->assertFalse($field->getRelatedFields());
    }

    private function getField($phase = Product::PLANT_PHASE_FINAL)
    {
        $player = $this->getTestPlayer();
        $product = $this->getTestProduct($player, 17); // carrot
        return $this->getTestField($product, $phase);
    }
}
