<?php

namespace Tests\Feature;

use App\Building\Farmland;
use App\Connector\WolniFarmerzyConnector;
use App\Field;
use App\Product;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Stream;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class WolniFarmerzyConnectorTest extends TestCase
{
    use DatabaseTransactions;

    public function testLogin()
    {
        $client = \Mockery::mock(Client::class)
            ->shouldReceive('request')
            ->andReturn($this->getLoginResponseMock())
            ->getMock();
        $wolniFarmerzyConnector = new WolniFarmerzyConnector($client);
        $this->assertTrue($wolniFarmerzyConnector->login($this->getTestPlayer()));
    }

    public function testLoginFail()
    {
        $stream1 = \Mockery::mock(Stream::class)
            ->shouldReceive('__toString')
            ->andReturn('[0,"Nazwa u\u017cytkownika lub has\u0142o s\u0105 b\u0142\u0119dne. Czy zapomnia\u0142e\u015b swoje dane dost\u0119pu <a href=\"javascript:void(0)\" onclick=\"setBox(\'pwforgotten\')\">w grze<\/a> lub <a href=\"https:\/\/pl.upjers.com\/passwordresend\" target=\"_blank\">na portalu Upjers?<\/a>?"]')
            ->shouldReceive('close')
            ->getMock();

        $responseMock = \Mockery::mock(Response::class)
            ->shouldReceive('getBody')
            ->once()
            ->andReturn($stream1)
            ->getMock();

        $client = \Mockery::mock(Client::class)
            ->shouldReceive('request')
            ->andReturn($responseMock)
            ->getMock();
        $wolniFarmerzyConnector = new WolniFarmerzyConnector($client);
        $this->assertFalse($wolniFarmerzyConnector->login($this->getTestPlayer()));
    }

    public function testDashboardCall()
    {
        $streamDashboard = \Mockery::mock(Stream::class);
        $streamDashboard->shouldReceive('__toString')
            ->andReturn($this->loadJSON('getFarmSuccess'))
            ->shouldReceive('close');

        $responseMock = $this->getLoginResponseMock();
        $responseMock->shouldReceive('getBody')
            ->once()
            ->andReturn($streamDashboard);

        $client = \Mockery::mock(Client::class);
        $client->shouldReceive('request')
            ->andReturn($responseMock);

        $wolniFarmerzyConnector = new WolniFarmerzyConnector($client);
        $this->assertTrue($wolniFarmerzyConnector->login($this->getTestPlayer()));
        $this->assertTrue(is_array($wolniFarmerzyConnector->getDashboardData()));
    }

    public function testSpaceFieldsCall()
    {
        $stream = \Mockery::mock(Stream::class);
        $stream->shouldReceive('__toString')
            ->andReturn($this->loadJSON('getGardeninitSuccess'))
            ->shouldReceive('close');

        $responseMock = $this->getLoginResponseMock();
        $responseMock->shouldReceive('getBody')
            ->once()
            ->andReturn($stream);

        $client = \Mockery::mock(Client::class);
        $client->shouldReceive('request')
            ->andReturn($responseMock);

        $player = $this->getTestPlayer();
        $farmland = new Farmland(['farm' => 2, 'position' => 2], $player);

        $wolniFarmerzyConnector = new WolniFarmerzyConnector($client);
        $this->assertTrue($wolniFarmerzyConnector->login($player));
        $this->assertTrue(is_array($wolniFarmerzyConnector->getFarmlandFields($farmland)));
    }

    public function testCollectCall()
    {
        $stream = \Mockery::mock(Stream::class);
        $stream->shouldReceive('__toString')
            ->andReturn($this->loadJSON('getGardeninitSuccess'))
            ->shouldReceive('close');

        $responseMock = $this->getLoginResponseMock();
        $responseMock->shouldReceive('getBody')
            ->once()
            ->andReturn($stream);

        $client = \Mockery::mock(Client::class);
        $client->shouldReceive('request')
            ->andReturn($responseMock);

        $player = $this->getTestPlayer();
        $farmland = new Farmland(['farm' => 2, 'position' => 2], $player);
        $field = new Field(3);
        $field->setProduct((new Product())->setPid(17));

        $wolniFarmerzyConnector = new WolniFarmerzyConnector($client);
        $this->assertTrue($wolniFarmerzyConnector->login($player));
        $this->assertTrue(is_array($wolniFarmerzyConnector->collect($farmland, $field)));
    }

    public function testSeedCall()
    {
        $stream = \Mockery::mock(Stream::class);
        $stream->shouldReceive('__toString')
            ->andReturn($this->loadJSON('getGardeninitSuccess'))
            ->shouldReceive('close');

        $responseMock = $this->getLoginResponseMock();
        $responseMock->shouldReceive('getBody')
            ->once()
            ->andReturn($stream);

        $client = \Mockery::mock(Client::class);
        $client->shouldReceive('request')
            ->andReturn($responseMock);

        $player = $this->getTestPlayer();
        $farmland = new Farmland(['farm' => 2, 'position' => 2], $player);
        $field = new Field(3);
        $field->setProduct((new Product())->setPid(17));

        $wolniFarmerzyConnector = new WolniFarmerzyConnector($client);
        $this->assertTrue($wolniFarmerzyConnector->login($player));
        $this->assertTrue(is_array($wolniFarmerzyConnector->seed($farmland, $field)));
    }

    public function testWaterCall()
    {
        $stream = \Mockery::mock(Stream::class);
        $stream->shouldReceive('__toString')
            ->andReturn($this->loadJSON('getGardeninitSuccess'))
            ->shouldReceive('close');

        $responseMock = $this->getLoginResponseMock();
        $responseMock->shouldReceive('getBody')
            ->once()
            ->andReturn($stream);

        $client = \Mockery::mock(Client::class);
        $client->shouldReceive('request')
            ->andReturn($responseMock);

        $player = $this->getTestPlayer();
        $farmland = new Farmland(['farm' => 2, 'position' => 2], $player);
        $field = new Field(3);
        $field->setProduct((new Product())->setPid(17));

        $wolniFarmerzyConnector = new WolniFarmerzyConnector($client);
        $this->assertTrue($wolniFarmerzyConnector->login($player));
        $this->assertTrue(is_array($wolniFarmerzyConnector->waterField($farmland, $field)));
    }

    private function getLoginResponseMock()
    {
        $streamLogin1 = \Mockery::mock(Stream::class);
        $streamLogin1->shouldReceive('__toString')
            ->andReturn($this->getLogin1Body())
            ->shouldReceive('close');

        $streamLogin2 = \Mockery::mock(Stream::class);
        $streamLogin2->shouldReceive('__toString')
            ->andReturn($this->getLogin2Body())
            ->shouldReceive('close');

        $responseMock = \Mockery::mock(Response::class);
        $responseMock->shouldReceive('getBody')
            ->once()
            ->andReturn($streamLogin1)
            ->shouldReceive('getBody')
            ->once()
            ->andReturn($streamLogin2);
        return $responseMock;
    }

    private function getLogin1Body()
    {
        return '[1,"https:\/\/wolnifarmerzy.pl\/portal\/port_logw.php?server=13&unr=12547546&portunr=16428914&token=4a85f5716830aaf78c92361f06d5e7af"]';
    }

    private function getLogin2Body()
    {
        return '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"  "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<!--[if gte IE 10]><meta http-equiv="X-UA-Compatible" content="IE=edge" /><![endif]-->
	<!--[if lte IE 9]><meta http-equiv="X-UA-Compatible" content="IE=8" /><![endif]-->
	<link href="http://s13.wolnifarmerzy.pl/css/main.php?v=0.50" rel="stylesheet" type="text/css">
	<link href="http://s13.wolnifarmerzy.pl/css/main2.php?v=0.50" rel="stylesheet" type="text/css">
	<link href="http://s13.wolnifarmerzy.pl/css/modules.php?v=0.50" rel="stylesheet" type="text/css">
	<link href="http://s13.wolnifarmerzy.pl/css/css2.php?v=0.50" rel="stylesheet" type="text/css">
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>Wolni farmerzy</title>
	<script type="text/javascript">
	var rackElement = Array();
	var rid = \'da513c47a2e86d9c44581fe80221be06\';
	var interval = null;
	var zeit = null;
	var _GFX = \'http://mff.wavecdn.de/mff/\';
	var PAYMENTLINK = \'https://www.up-pay.com/?user=12547568&game=11&land=pl&server=13&hash=ed1d538398aa02e36b95bd2ca137a7d1\';
    var travelad_go = 0;
    var travelad_hash = \'b9ef044d6fc0190dd767065821eabf97\';
    var travelad_url = \'https://poxmediagroup.de/mff/proc.php?userid=12547568&publisherZone=mff&rd=\';
	var t_regal_leer = \'Ten magazyn jest pusty\';
var t_anpflanzen = \'Zasiej\';
var t_setzen = \'Zasadź\';';
    }
}
