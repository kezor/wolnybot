<?php

namespace Tests\Feature;

use App\Building\Farmland;
use App\Connector\WolniFarmerzyConnector;
use App\Product;
use App\ProductSizeService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class FarmlandTest extends TestCase
{

    use DatabaseTransactions;

    public function testFarmland()
    {
        return;
        $player = $this->getTestPlayer();

        $farmland = new Farmland(['farm' => 1, 'position' => 2], $player);

        $product = $this->getTestProduct($player, 17);

        $connectorMock = \Mockery::mock(WolniFarmerzyConnector::class)
            ->shouldReceive('collect')
            ->times(3)
            ->shouldReceive('seed')
            ->times(119)
            ->shouldReceive('waterField')
            ->times(120)
            ->getMock();

        $farmland->setConnector($connectorMock);

        $this->assertEquals(2, $farmland->getPosition());
        $this->assertEquals(1, $farmland->getFarmId());

        $fieldReadyToCollectData1x1 = [
            'teil_nr' => '2',
            'inhalt' => '20',
            'gepflanzt' => '1497191115',
            'zeit' => '1497218475',
            'wasser' => '1497191120',
            'guild' => '0',
            'buildingid' => 'v',
            'x' => 1,
            'y' => 1,
            'iswater' => true,
            'phase' => 4
        ];

        $fieldReadyToCollectData2x1 = [
            'teil_nr' => '3',
            'inhalt' => '20',
            'gepflanzt' => '1497191115',
            'zeit' => '1497218475',
            'wasser' => '1497191120',
            'guild' => '0',
            'buildingid' => 'v',
            'x' => 2,
            'y' => 1,
            'iswater' => true,
            'phase' => 4
        ];

        $fieldReadyToCollectData2x2 = [
            'teil_nr' => '5',
            'inhalt' => '20',
            'gepflanzt' => '1497191115',
            'zeit' => '1497218475',
            'wasser' => '1497191120',
            'guild' => '0',
            'buildingid' => 'v',
            'x' => 2,
            'y' => 1,
            'iswater' => true,
            'phase' => 4
        ];

        $fieldInProgressData = [
            'teil_nr' => '13',
            'inhalt' => '20',
            'gepflanzt' => '1497191115',
            'zeit' => '1497218475',
            'wasser' => '1497191120',
            'guild' => '0',
            'buildingid' => 'v',
            'x' => 1,
            'y' => 1,
            'iswater' => true,
            'phase' => 3
        ];

        $farmland->updateField($fieldReadyToCollectData1x1);
        $farmland->updateField($fieldReadyToCollectData2x1);
        $farmland->updateField($fieldReadyToCollectData2x2);
        $farmland->updateField($fieldInProgressData);

        $farmland->process();
    }

    /**
     * @dataProvider getDataToFarmland
     */
    public function testFarmlandNotReadyToCollect($prepareProductPid, $preparePhase, $collectCount, $productToSeed, $productToSeedCount, $seedCount, $waterCount)
    {
        $player = $this->getTestPlayer();

        $farmland = new Farmland(['farm' => 2, 'position' => 6], $player);

        $product = $this->getTestProduct($player, $productToSeed, $productToSeedCount);

        $connectorMock = \Mockery::mock(WolniFarmerzyConnector::class)
            ->shouldReceive('collect')
            ->times($collectCount)
            ->shouldReceive('seed')
            ->times($seedCount)
            ->shouldReceive('waterField')
            ->times($waterCount)
            ->getMock();

        $farmland->setConnector($connectorMock);

        $this->assertEquals(6, $farmland->getPosition());
        $this->assertEquals(2, $farmland->getFarmId());

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

        $farmland->process();
    }

    public function getDataToFarmland()
    {
        return [
            [
                'prepareProductPid' => 17, //carrot
                'preparePhase' => Product::PLANT_PHASE_BEGIN,
                'collectCount' => 0,
                'productToSeed' => 17,  //carrot
                'productToSeedCount' => 120,
                'seedCount' => 0,
                'waterCount' => 0,
            ],
            [
                'prepareProductPid' => 17, //carrot
                'preparePhase' => Product::PLANT_PHASE_FINAL,
                'collectCount' => 120,
                'productToSeed' => 17,  //carrot
                'productToSeedCount' => 120,
                'seedCount' => 120,
                'waterCount' => 120,
            ],
            [
                'prepareProductPid' => 1, //wheat
                'preparePhase' => Product::PLANT_PHASE_FINAL,
                'collectCount' => 60,
                'productToSeed' => 17,  //carrot
                'productToSeedCount' => 120,
                'seedCount' => 120,
                'waterCount' => 120,
            ],
            [
                'prepareProductPid' => 2, //corn
                'preparePhase' => Product::PLANT_PHASE_FINAL,
                'collectCount' => 30,
                'productToSeed' => 17,  //carrot
                'productToSeedCount' => 120,
                'seedCount' => 120,
                'waterCount' => 120,
            ],
            [
                'prepareProductPid' => 2, //corn
                'preparePhase' => Product::PLANT_PHASE_FINAL,
                'collectCount' => 30,
                'productToSeed' => 2,  //corn
                'productToSeedCount' => 120,
                'seedCount' => 30,
                'waterCount' => 30,
            ],
            [
                'prepareProductPid' => 1, //wheat
                'preparePhase' => Product::PLANT_PHASE_FINAL,
                'collectCount' => 60,
                'productToSeed' => 1,  //wheat
                'productToSeedCount' => 120,
                'seedCount' => 60,
                'waterCount' => 60,
            ],
            [
                'prepareProductPid' => 1, //wheat
                'preparePhase' => Product::PLANT_PHASE_FINAL,
                'collectCount' => 60,
                'productToSeed' => 2,  //corn
                'productToSeedCount' => 10,
                'seedCount' => 10,
                'waterCount' => 10,
            ],
        ];
    }


}
