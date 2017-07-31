<?php

use App\Building\Hovel;

class HovelTest extends \Tests\TestCase
{


    public function testHoverInProgressCanFeed()
    {
        $player = $this->getTestPlayer();

        $hovel = new Hovel(['farm' => 2, 'position' => 6], $player);

        $product = $this->getTestProduct($player, 1, 1000);

        $connectorMock = \Mockery::mock(\App\Connector\WolniFarmerzyConnector::class)
            ->shouldReceive('initHovel')
            ->times(1)
            ->andReturn($this->getHovelData())
            ->shouldReceive('collectEggs')
            ->times(0)
            ->shouldReceive('feedChickens')
            ->times(34)
            ->getMock();

        $hovel->setConnector($connectorMock);

        $this->assertEquals(6, $hovel->getPosition());
        $this->assertEquals(2, $hovel->getFarmId());
        $hovel->process();
    }

    public function testHoverInProgressCantFeed()
    {
        $player = $this->getTestPlayer();

        $hovel = new Hovel(['farm' => 2, 'position' => 6], $player);

        $product = $this->getTestProduct($player, 1, 1000);

        $connectorMock = \Mockery::mock(\App\Connector\WolniFarmerzyConnector::class)
            ->shouldReceive('initHovel')
            ->times(1)
            ->andReturn($this->getHovelData2())
            ->shouldReceive('collectEggs')
            ->times(0)
            ->shouldReceive('feedChickens')
            ->times(0)
            ->getMock();

        $hovel->setConnector($connectorMock);

        $this->assertEquals(6, $hovel->getPosition());
        $this->assertEquals(2, $hovel->getFarmId());
        $hovel->process();
    }

}
