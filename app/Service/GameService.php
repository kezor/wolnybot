<?php

namespace App\Service;


use App\Building\Farmland;
use App\Building\Hovel;
use App\BuildingType;
use App\Connector\ConnectorInterface;
use App\Connector\WolniFarmerzyConnector;
use App\Farm;
use App\Field;
use App\Player;
use App\ProductCategoryMapper;
use App\Repository\FieldRepository;
use App\Repository\SpaceRepository;
use App\Repository\ProductRepository;
use App\Space;
use App\Product;

class GameService
{
    /**
     * @var ConnectorInterface|WolniFarmerzyConnector
     */
    private $connector;

    private $farms;

    /**
     * @var Player
     */
    private $player;

    public function __construct(Player $player, ConnectorInterface $connector = null)
    {
        if (!$connector) {
            $connector = new WolniFarmerzyConnector();
        }
        $this->connector = $connector;
        $this->player    = $player;
        $logged = $this->connector->login($player);

        if($logged){
            $this->updateSpacesData();
            $this->updateStock();
        }else{
            throw new \Exception('User not logged in');
        }

    }

    public function run()
    {
        /** @var Farm $farm */
        foreach ($this->farms as $farm) {
            $farm->process();
        }
    }

    public function updateStock()
    {
        $dashboardData = $this->connector->getDashboardData();

        $stocks = $dashboardData['updateblock']['stock']['stock'];

        $updatedItemsInStock = [];

        foreach ($stocks as $product) {
            foreach ($product as $level1) {
                foreach ($level1 as $level2) {
                    /** @var Product $product */
                    $product         = ProductRepository::getStock($level2, $this->player);
                    $product->amount = $level2['amount'];
                    $product->save();
                    $updatedItemsInStock[] = $product->id;
                }
            }
        }

        $emptyItemsInStock = ProductRepository::getEmptyItems($updatedItemsInStock, $this->player);

        /** @var Product $item */
        foreach ($emptyItemsInStock as $item) {
            $item->amount = 0;
            $item->save();
        }
    }


    public function updateSpacesData()
    {
        $dashboardData = $this->connector->getDashboardData();

        $farms = $dashboardData['updateblock']['farms']['farms'];

        foreach ($farms as $farmId => $farm) {
            $this->farms[$farmId] = new Farm();
            foreach ($farm as $spaceData) {
                if ($spaceData['status'] == 1) {
                    switch ($spaceData['buildingid']) {
                        case BuildingType::FARMLAND:
                            $this->processFarmland($spaceData, $farmId);
                            break;
                        case BuildingType::HOVEL:
                            $this->processHovel($spaceData, $farmId);
                            break;
                    }
                    $this->usedSeeds = []; // reset used products for new space}
                }


            }
        }
    }

    private function processFarmland($spaceData, $farmId)
    {
        $farmland = new Farmland($spaceData, $this->player);
        $farmland->setConnector($this->connector);

        $fieldsData = $this->connector->getSpaceFields($farmland);
        $fields     = $fieldsData['datablock'][1];

        if ($fields != 0) {
            foreach ($fields as $key => $fieldData) {
                if (!is_numeric($key)) {
                    continue;
                }
                $farmland->updateField($fieldData);
            }

        }
        $this->farms[$farmId]->addFarmland($farmland);
    }

    private function processHovel($spaceData, $farmId)
    {
        $hovel = new Hovel($spaceData, $this->player);
        $hovel->setConnector($this->connector);
        $this->farms[$farmId]->addBuilding($hovel);

        return $this;
    }

    public function disableTutorial()
    {
        $space           = new Space();
        $space->farm     = 1;
        $space->position = 1;
        //OK http://s8.wolnifarmerzy.pl/ajax/farm.php?rid=fe3faac43740b3f28e6d6bba45c633cb&mode=getbuildingoptions&farm=1&position=1
        $this->connector->getBuildingsOptions($space);


        //OK http://s8.wolnifarmerzy.pl/ajax/farm.php?rid=fe3faac43740b3f28e6d6bba45c633cb&mode=buybuilding&farm=1&position=1&id=1&buildingid=1
        $farmland = new Farmland();
        $this->connector->buyBuilding($space, $farmland);

        //OK http://s8.wolnifarmerzy.pl/ajax/farm.php?rid=fe3faac43740b3f28e6d6bba45c633cb&mode=gardeninit&farm=1&position=1
        $this->connector->getSpaceFields($space);

        //http://s8.wolnifarmerzy.pl/ajax/farm.php?rid=fe3faac43740b3f28e6d6bba45c633cb&mode=garden_plant&farm=1&position=1&pflanze[]=17&feld[]=3&felder[]=3&cid=12
        $firstField        = new Field();
        $firstField->index = 1;
        $carrot            = new Product($firstField);
        $this->connector->seed($carrot->getPid());

        //http://s8.wolnifarmerzy.pl/ajax/farm.php?rid=fe3faac43740b3f28e6d6bba45c633cb&mode=garden_water&farm=1&position=1&feld[]=3&felder[]=3
        $this->connector->waterField($firstField);

        //wait 15-20 sec
        echo 'Sleep for 20 seconds' . PHP_EOL;
        for ($i = 0; $i < 20; $i++) {
            echo 20 - $i . ' ';
            sleep(1);
        }
        echo PHP_EOL;
        //http://s8.wolnifarmerzy.pl/ajax/farm.php?rid=fe3faac43740b3f28e6d6bba45c633cb&mode=garden_harvest&farm=1&position=1&pflanze[]=17&feld[]=3&felder[]=3
        $this->connector->collect($firstField);

        $this->connector->increaseTutorialStep();
        $this->connector->closeTutorial();

        //second step tutorial
        for ($i = 0; $i < 3; $i++) {
            $this->connector->increaseTutorialStep();
        }

        $this->connector->closeTutorial();
        $this->player->active = true;
        $this->player->save();
    }
}