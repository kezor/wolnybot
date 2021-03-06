<?php

namespace Tests\Unit;


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
        $this->assertTrue($fieldReadyToCollect->isReadyToCrop());

        $fieldNotReadyToCollect = $this->getField(Product::PLANT_PHASE_BEGIN);

        $this->assertNotNull($fieldReadyToCollect->getProduct());
        $this->assertFalse($fieldNotReadyToCollect->isReadyToCrop());
    }

    public function testSetAsEmpty()
    {
        $field = $this->getField();

        $this->assertTrue($field->isReadyToCrop());

        $field->removeProduct();

        $this->assertFalse($field->isReadyToCrop());
        $this->assertNull($field->getProduct());
        $this->assertEquals(Product::PLANT_PHASE_EMPTY, $field->getPhase());
        $this->assertEquals(null, $field->getProductPid());
        $this->assertEquals(0, $field->time);
        $this->assertEquals(0, $field->getPlanted());
    }

    public function testGetRelatedFields()
    {
        $field = $this->getField();

        $this->assertTrue($field->isReadyToCrop());
        $field->removeProduct();
        $this->assertFalse($field->getRelatedFields());
    }

    private function getField($phase = Product::PLANT_PHASE_FINAL)
    {
        $player = $this->getTestPlayer();
        $product = $this->getTestProduct($player, 17); // carro
        //t
        return $this->getTestField($product, ['phase' => $phase]    );
    }
}
