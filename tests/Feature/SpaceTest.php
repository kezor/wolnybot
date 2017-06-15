<?php

namespace Tests\Feature;

use App\BuildingType;
use App\Field;
use App\Space;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SpaceTest extends TestCase
{

    use DatabaseTransactions;

    /**
     * @var Space
     */
    private $space;

    public function setUp()
    {
        parent::setUp();

        $player = $this->getTestPlayer();

        $this->space = $this->getTestSpace($player, BuildingType::FARMLAND);
    }

    public function testGetFields()
    {
        $fieldsData = [
            ['phase' => 0],
            ['phase' => 0],
            ['phase' => 0],
            ['phase' => 4],
            ['phase' => 4],
            ['phase' => 4],
            ['phase' => 4],
            ['phase' => 4],
        ];

        $this->addFieldsIntoSpace($fieldsData);

        $this->assertCount(8, $this->space->getFields());
    }

    public function testGetFieldsToCollect()
    {
        $fieldsData = [
            ['phase' => 0],
            ['phase' => 0],
            ['phase' => 0],
            ['phase' => 4],
            ['phase' => 4],
            ['phase' => 4],
            ['phase' => 4],
            ['phase' => 4],
        ];

        $this->addFieldsIntoSpace($fieldsData);

        $this->assertCount(5, $this->space->getFieldsToCollect());
    }

    public function testGetPlayer()
    {
        $this->assertNotNull($this->space->getPlayer());
    }

    private function addFieldsIntoSpace($fieldsData)
    {
        $i = 1;
        $product = $this->getTestProduct();

        foreach ($fieldsData as $data){
            $this->getTestField($product, $this->space, $data['phase']);
            $i++;
        }
    }

}
