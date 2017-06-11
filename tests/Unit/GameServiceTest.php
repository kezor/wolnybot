<?php

namespace Tests\Unit;


use App\Connector\WolniFarmerzyConnector;
use App\Field;
use App\Product;
use App\Service\GameService;
use App\Space;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class GameServiceTest extends TestCase
{
    use DatabaseTransactions;
    use DatabaseMigrations;

    public function testUpdateStock()
    {
        $player = $this->getTestPlayer();

        $connectorMock = \Mockery::mock(WolniFarmerzyConnector::class)
            ->shouldReceive('login')
            ->andReturn(true)
            ->shouldReceive('getDashboardData')
            ->andReturn($this->getDashboardSuccessData())
            ->getMock();

        $gameService = new GameService($player, $connectorMock);

        $gameService->updateStock();

        $allProductsInDatabase = Product::all();

        $this->assertNotNull($allProductsInDatabase);

        $this->assertCount(5, $allProductsInDatabase);
    }

    public function testUpdateFields()
    {
        $player = $this->getTestPlayer();

        $connectorMock = \Mockery::mock(WolniFarmerzyConnector::class)
            ->shouldReceive('login')
            ->andReturn(true)
            ->shouldReceive('getDashboardData')
            ->andReturn($this->getDashboardSuccessData())
            ->shouldReceive('getSpaceFields')
            ->andReturn($this->getGardeninitSuccessData())
            ->getMock();

        $gameService = new GameService($player, $connectorMock);

        $gameService->updateFields();

        $allSpaces = Space::all();
        $this->assertNotNull($allSpaces);
        $this->assertCount(1, $allSpaces);

        $allFields = Field::all();
        $this->assertNotNull($allFields);
        $this->assertCount(120, $allFields);

        $gameService->updateFields();

        $allSpaces = Space::all();
        $this->assertNotNull($allSpaces);
        $this->assertCount(1, $allSpaces);

        $allFields = Field::orderBy('index', 'ASC')->get();
        $this->assertNotNull($allFields);
        $this->assertCount(120, $allFields);

        /** @var Field $field */
        foreach ($allFields as $field){
            $this->assertEquals(1, $field->space);
        }
    }

    private function getDashboardSuccessData()
    {
        return json_decode($this->loadJSON('getFarmSuccess'), true);
    }

    private function getGardeninitSuccessData()
    {
        return json_decode($this->loadJSON('getGardeninitSuccess'), true);
    }
}