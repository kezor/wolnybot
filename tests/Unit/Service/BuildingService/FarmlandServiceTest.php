<?php

namespace Tests\Unit\Service\BuildingService;

use App\Connector\WolniFarmerzyConnector;
use App\Facades\ActivitiesService;
use App\Product;
use App\ProductSizeService;
use App\Service\BuildingsService\FarmlandService;
use Tests\TestCase;

class FarmlandServiceTest extends TestCase
{

    public function testSeedFunctionCarrot()
    {
        $player = $this->getTestPlayer();

        $connectorMock = \Mockery::mock(WolniFarmerzyConnector::class)
            ->shouldReceive('login')
            ->andReturn(true)
            ->shouldReceive('seed')
            ->getMock();

        $farm = $this->getTestFarm($player, ['farm_id' => 2]);

        $farmland = $this->getTestFarmland($farm, ['position' => 6]);

        /** @var Product $product */
        $product = $this->getTestProduct($player, 17, 1000);

        $farmlandService = new FarmlandService($player, $connectorMock);

        ActivitiesService::shouldReceive('foundReadyToSeed')->once()->withArgs([$farmland, 120]);
        ActivitiesService::shouldReceive('seededFields')->once()->withArgs([$farmland, 120, $product]);

        $farmlandService->seedPlants($farmland, $product);
    }

    public function testSeedFunctionCorn()
    {
        $player = $this->getTestPlayer();

        $connectorMock = \Mockery::mock(WolniFarmerzyConnector::class)
            ->shouldReceive('login')
            ->andReturn(true)
            ->shouldReceive('seed')
            ->getMock();

        $farm = $this->getTestFarm($player, ['farm_id' => 2]);

        $farmland = $this->getTestFarmland($farm, ['position' => 6]);

        /** @var Product $product */
        $product = $this->getTestProduct($player, 4, 1000);

        $farmlandService = new FarmlandService($player, $connectorMock);

        ActivitiesService::shouldReceive('foundReadyToSeed')->once()->withArgs([$farmland, 120]);
        ActivitiesService::shouldReceive('seededFields')->once()->withArgs([$farmland, 30, $product]);
        $farmlandService->seedPlants($farmland, $product);
    }

    /**
     * @dataProvider getDataToFarmland
     */
    public function testFarmlandNotReadyToCollect($prepareProductPid, $preparePhase, $collectCount, $productToSeed, $productToSeedStockAmount, $seedCount, $waterCount)
    {
        $player = $this->getTestPlayer();

        $farm = $this->getTestFarm($player, ['farm_id' => 2]);

        $farmland = $this->getTestFarmland($farm, ['position' => 6]);

        /** @var Product $product */
        $product = $this->getTestProduct($player, $productToSeed, $productToSeedStockAmount);

        $connectorMock = \Mockery::mock(WolniFarmerzyConnector::class)
            ->shouldReceive('login')
            ->andReturn(true)
            ->shouldReceive('collect')
            ->times($collectCount)
            ->shouldReceive('seed')
            ->times($seedCount)
            ->shouldReceive('waterField')
            ->times($waterCount)
            ->getMock();

        $farmlandService = new FarmlandService($player, $connectorMock);

        $this->assertEquals(6, $farmland->getPosition());
        $this->assertEquals(2, $farmland->farm->farm_id);

        for ($i = 1; $i <= 120; $i++) {
            $fieldData = [
                'teil_nr' => $i,
                'inhalt' => $prepareProductPid,
                'gepflanzt' => '1497191115',
                'zeit' => '1497218475',
                'wasser' => '1497191120',
                'guild' => '0',
                'buildingid' => 'v',
                'x' => ProductSizeService::getProductLenghtByPid($prepareProductPid),
                'y' => ProductSizeService::getProductHeightByPid($prepareProductPid),
                'iswater' => true,
                'phase' => $preparePhase
            ];
            $farmland->updateField($fieldData);
        }

        ActivitiesService::shouldReceive('foundReadyToCollect')->once()->withArgs([$farmland, $collectCount]);
        ActivitiesService::shouldReceive('collectedFields')->once()->withArgs([$farmland, $collectCount]);
        ActivitiesService::shouldReceive('foundReadyToSeed')->once()->withArgs([$farmland, $collectCount > 0 ? 120 : 0]);
        ActivitiesService::shouldReceive('seededFields')->once()->withArgs([$farmland, $seedCount, $product]);

        $farmlandService->collectReadyPlants($farmland);
        $farmlandService->seedPlants($farmland, $product);
        $farmlandService->waterPlants($farmland);
    }

    public function getDataToFarmland()
    {
        return [
            [
                'prepareProductPid' => 17, //carrot
                'preparePhase' => Product::PLANT_PHASE_BEGIN,
                'collectCount' => 0,
                'productToSeed' => 17,  //carrot
                'productToSeedStockAmount' => 120,
                'seedCount' => 0,
                'waterCount' => 0,
            ],
            [
                'prepareProductPid' => 17, //carrot
                'preparePhase' => Product::PLANT_PHASE_FINAL,
                'collectCount' => 120,
                'productToSeed' => 17,  //carrot
                'productToSeedStockAmount' => 120,
                'seedCount' => 120,
                'waterCount' => 120,
            ],
            [
                'prepareProductPid' => 1, //wheat
                'preparePhase' => Product::PLANT_PHASE_FINAL,
                'collectCount' => 60,
                'productToSeed' => 17,  //carrot
                'productToSeedStockAmount' => 120,
                'seedCount' => 120,
                'waterCount' => 120,
            ],
            [
                'prepareProductPid' => 2, //corn
                'preparePhase' => Product::PLANT_PHASE_FINAL,
                'collectCount' => 30,
                'productToSeed' => 17,  //carrot
                'productToSeedStockAmount' => 120,
                'seedCount' => 120,
                'waterCount' => 120,
            ],
            [
                'prepareProductPid' => 2, //corn
                'preparePhase' => Product::PLANT_PHASE_FINAL,
                'collectCount' => 30,
                'productToSeed' => 2,  //corn
                'productToSeedStockAmount' => 120,
                'seedCount' => 30,
                'waterCount' => 30,
            ],
            [
                'prepareProductPid' => 1, //wheat
                'preparePhase' => Product::PLANT_PHASE_FINAL,
                'collectCount' => 60,
                'productToSeed' => 1,  //wheat
                'productToSeedStockAmount' => 120,
                'seedCount' => 60,
                'waterCount' => 60,
            ],
            [
                'prepareProductPid' => 1, //wheat
                'preparePhase' => Product::PLANT_PHASE_FINAL,
                'collectCount' => 60,
                'productToSeed' => 2,  //corn
                'productToSeedStockAmount' => 10,
                'seedCount' => 10,
                'waterCount' => 10,
            ],
        ];
    }
}
