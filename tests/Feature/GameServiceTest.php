<?php

namespace Tests\Feature;


use App\Connector\WolniFarmerzyConnector;
use App\Field;
use App\Product;
use App\Service\GameService;
use App\Space;
use Tests\TestCase;

class GameServiceTest extends TestCase
{
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

        $allProductsInDatabase = Product::where('player', $player->id)->get();

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

        $gameService->updateSpacesData();

        $allSpaces = Space::where('player', $player->id)->get();
        $this->assertNotNull($allSpaces);
        $this->assertCount(1, $allSpaces);

        $allFields = Field::where('space', $allSpaces[0]->id)->get();
        $this->assertNotNull($allFields);
        $this->assertCount(120, $allFields);

        $gameService->updateSpacesData();

        $allSpaces = Space::where('player', $player->id)->get();
        $this->assertNotNull($allSpaces);
        $this->assertCount(1, $allSpaces);

        $allFields = Field::where('space', $allSpaces[0]->id)->orderBy('index', 'ASC')->get();
        $this->assertNotNull($allFields);
        $this->assertCount(120, $allFields);

        /** @var Field $field */
        foreach ($allFields as $field){
            $this->assertEquals($allSpaces[0]->id, $field->space);
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