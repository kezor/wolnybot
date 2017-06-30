<?php

namespace Tests\Feature;


use App\Connector\WolniFarmerzyConnector;
use App\Field;
use App\Product;
use App\Service\GameService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class GameServiceTest extends TestCase
{
    use DatabaseTransactions;

    public function testUpdateStock()
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
    }

    public function testRunTest()
    {
        $player = $this->getTestPlayer();

        $connectorMock = \Mockery::mock(WolniFarmerzyConnector::class)
            ->shouldReceive('login')
            ->andReturn(true)
            ->shouldReceive('getDashboardData')
            ->andReturn($this->getDashboardSuccessData())
            ->shouldReceive('getSpaceFields')
            ->andReturn($this->getGardeninitSuccessData())
            ->shouldReceive('collect')
            ->times(10)
            ->shouldReceive('seed')
            ->times(10)
            ->shouldReceive('waterField')
            ->times(102)
            ->getMock();

        $gameService = new GameService($player, $connectorMock);
        $gameService->run();
    }
}