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

    private $player;

    public function setUp()
    {
        parent::setUp();

        $this->player = $this->getTestPlayer();

        $this->space = $this->getTestSpace($this->player, BuildingType::FARMLAND);
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

        $this->addFieldsIntoSpace($fieldsData, $this->player);

        $this->assertCount(8, $this->space->getFields());
    }

    public function testGetFieldsNotFarmland()
    {

        $player = $this->getTestPlayer();

        $space = $this->getTestSpace($player, BuildingType::HOVEL);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('You can use this method only for farmland');

        $space->getFields();
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

        $player = $this->getTestPlayer();

        $this->addFieldsIntoSpace($fieldsData, $player);

        $this->assertCount(5, $this->space->getFieldsToCollect());
    }

    public function testGetFieldsToCollectNotFarmland()
    {
        $player = $this->getTestPlayer();

        $space = $this->getTestSpace($player, BuildingType::HOVEL);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('You can use this method only for farmland');

        $space->getFieldsToCollect();
    }

    public function testGetPlayer()
    {
        $this->assertNotNull($this->space->getPlayer());
    }

    public function testIsFieldsInDatabase()
    {
        $player = $this->getTestPlayer();

        $spaceFarmland1 = $this->getTestSpace($player, BuildingType::FARMLAND, true);
        $this->assertTrue($spaceFarmland1->isFieldsInDatabase());

        $spaceFarmland2 = $this->getTestSpace($player, BuildingType::FARMLAND, false);
        $this->assertFalse($spaceFarmland2->isFieldsInDatabase());

        $spaceFarmland2 = $this->getTestSpace($player, BuildingType::HOVEL, true);
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('You can use this method only for farmland');
        $spaceFarmland2->isFieldsInDatabase();
    }

    private function addFieldsIntoSpace($fieldsData, $player)
    {
        $i = 1;
        $product = $this->getTestProduct($player);

        foreach ($fieldsData as $data){
            $this->getTestField($product, $this->space, $data['phase']);
            $i++;
        }
    }

}
