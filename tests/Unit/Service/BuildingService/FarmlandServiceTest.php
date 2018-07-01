<?php
/**
 * Created by PhpStorm.
 * User: maciek
 * Date: 01.07.18
 * Time: 14:42
 */

namespace Tests\Unit\Service\BuildingService;

use App\Connector\WolniFarmerzyConnector;
use App\Product;
use App\ProductSizeService;
use App\Service\BuildingsService\FarmlandService;
use Tests\TestCase;

class FarmlandServiceTest extends TestCase
{
    /**
     * @dataProvider getDataToFarmland
     */
    public function testFarmlandNotReadyToCollect($prepareProductPid, $preparePhase, $collectCount, $productToSeed, $productToSeedStockAmount, $seedCount, $waterCount)
    {
        $player = $this->getTestPlayer();

        $farm = $this->getTestFarm($player, ['farm_id' => 2]);

        $farmland = $this->getTestFarmland($farm, ['position' => 6]);

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
