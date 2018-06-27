<?php

namespace App\Service;


use App\Building\Farmland;
use App\BuildingType;
use App\Connector\ConnectorInterface;
use App\Connector\WolniFarmerzyConnector;
use App\Field;
use App\Player;
use App\Repository\FarmlandRepository;
use App\Repository\FarmRepository;
use App\Repository\ProductRepository;
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

    private $loggedIn = false;

    public function __construct(Player $player, ConnectorInterface $connector = null)
    {
        if (!$connector) {
            $connector = new WolniFarmerzyConnector();
        }

        $this->connector = $connector;
        $this->player = $player;
        $this->loggedIn = $this->connector->login($player);
    }

    /**
     * @return bool
     */
    public function isPlayerLoggedIn()
    {
        return $this->loggedIn;
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
                    $product = ProductRepository::getStock($level2, $this->player);
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


    public function updateBuildings()
    {
        $dashboardData = $this->connector->getDashboardData();

        $farms = $dashboardData['updateblock']['farms']['farms'];

        foreach ($farms as $farmId => $farmData) {
            $farm = FarmRepository::getFarm($farmId, $this->player);
            foreach ($farmData as $spaceData) {
                if ($spaceData['status'] == 1) {
                    switch ($spaceData['buildingid']) {
                        case BuildingType::FARMLAND:
//                            var_dump('Updating....');
                            $farmland = FarmlandRepository::getFarmland($farm, $this->player, $spaceData);
                            $farmland->fillInFields();
                            $this->updateFields($farmland);
                            break;
//                        case BuildingType::HOVEL:
//                            $this->processHovel($spaceData, $farmId);
//                            break;
                    }
                    $this->usedSeeds = []; // reset used products for new space}
                }


            }
        }
    }

    public function updateFields(Farmland $farmland)
    {
        $fieldsData = $this->connector->getFarmlandFields($farmland);
        $fields     = $fieldsData['datablock'][1];

        $updatedFieldIndexes = [];

        if ($fields != 0) {
            foreach ($fields as $key => $fieldData) {
                if (!is_numeric($key)) {
                    continue;
                }
                $farmland->updateField($fieldData);
                $updatedFieldIndexes[] = $fieldData['teil_nr'];
            }
        }
        $farmland->clearFields($updatedFieldIndexes);
    }

//    private function processHovel($spaceData, $farmId)
//    {
//        $hovel = new Hovel($spaceData, $this->player);
//        $hovel->setConnector($this->connector);
//        $this->farms[$farmId]->addBuilding($hovel);
//
//        return $this;
//    }

//    public function disableTutorial()
//    {
//        $space = new Space();
//        $space->farm = 1;
//        $space->position = 1;
//        //OK http://s8.wolnifarmerzy.pl/ajax/farm.php?rid=fe3faac43740b3f28e6d6bba45c633cb&mode=getbuildingoptions&farm=1&position=1
//        $this->connector->getBuildingsOptions($space);
//
//
//        //OK http://s8.wolnifarmerzy.pl/ajax/farm.php?rid=fe3faac43740b3f28e6d6bba45c633cb&mode=buybuilding&farm=1&position=1&id=1&buildingid=1
//        $farmland = new Farmland();
//        $this->connector->buyBuilding($space, $farmland);
//
//        //OK http://s8.wolnifarmerzy.pl/ajax/farm.php?rid=fe3faac43740b3f28e6d6bba45c633cb&mode=gardeninit&farm=1&position=1
//        $this->connector->getSpaceFields($space);
//
//        //http://s8.wolnifarmerzy.pl/ajax/farm.php?rid=fe3faac43740b3f28e6d6bba45c633cb&mode=garden_plant&farm=1&position=1&pflanze[]=17&feld[]=3&felder[]=3&cid=12
//        $firstField = new Field(1);
//        $firstField->index = 1;
//        $carrot = new Product($firstField);
//        $this->connector->seed($carrot->getPid());
//
//        //http://s8.wolnifarmerzy.pl/ajax/farm.php?rid=fe3faac43740b3f28e6d6bba45c633cb&mode=garden_water&farm=1&position=1&feld[]=3&felder[]=3
//        $this->connector->waterField($firstField);
//
//        //wait 15-20 sec
//        echo 'Sleep for 20 seconds' . PHP_EOL;
//        for ($i = 0; $i < 20; $i++) {
//            echo 20 - $i . ' ';
//            sleep(1);
//        }
//        echo PHP_EOL;
//        //http://s8.wolnifarmerzy.pl/ajax/farm.php?rid=fe3faac43740b3f28e6d6bba45c633cb&mode=garden_harvest&farm=1&position=1&pflanze[]=17&feld[]=3&felder[]=3
//        $this->connector->collect($firstField);
//
//        $this->connector->increaseTutorialStep();
//        $this->connector->closeTutorial();
//
//        //second step tutorial
//        for ($i = 0; $i < 3; $i++) {
//            $this->connector->increaseTutorialStep();
//        }
//
//        $this->connector->closeTutorial();
//        $this->player->active = true;
//        $this->player->save();
//    }

    public function collectReadyPlants(Farmland $farmland)
    {
        /** @var Field $finalFieldToReset */
        foreach ($farmland->fields as $finalFieldToReset) {
            if ($finalFieldToReset->canCollect()) {
                $this->resetRelatedFields($farmland, $finalFieldToReset);
                $this->connector->collect($farmland, $finalFieldToReset);
                $finalFieldToReset->removeProduct();
            }
        }
    }

    private function resetRelatedFields(Farmland $farmland, Field $field)
    {
        for ($i = 0; $i < $field->getOffsetX(); $i++) {
            for ($j = 0; $j < $field->getOffsetY(); $j++) {
                $indexToRemove = $i + $field->getIndex() + ($j * 12);
                if ($indexToRemove !== $field->getIndex()) {
                    $farmland->fields[$indexToRemove]->removeProduct();
                }
            }
        }
    }

    public function seedPlants(Farmland $farmland, Product $productToSeed)
    {
        $emptyFields = $farmland->getEmptyFields();

        $fieldsToSeed = $this->selectFields($emptyFields, $productToSeed);

        $responseData = null;

//        while (!empty($fieldsToSeed)) {
//            reset($fieldsToSeed);
            /** @var Field[] $fieldsToSeed */
            foreach ($fieldsToSeed as $field) {
                $responseData = $this->connector->seed($farmland, $field);
                $farmland->updateField([
                    'teil_nr' => $field->getIndex(),
                    'inhalt' => $field->getProduct()->getPid(),
                    'x' => $field->getProduct()->getLength(),
                    'y' => $field->getProduct()->getHeight(),
                    'phase' => Product::PLANT_PHASE_BEGIN,
                    'gepflanzt' => time(),
                    'zeit' => time(),
                    'iswater' => false,
                ]);
            }

//            $fieldsToSeed = $farmland->getFieldsToSeed();
//        }
        if (null !== $responseData) {
            $remain = $responseData['updateblock']['farms']['farms']['1']['1']['production']['0']['remain'];
            $farmland->remain = time() + $remain;
            $farmland->save();
        }
    }

//    public function getFieldsToSeed($emptyFields, Product $productToSeed)
//    {
//        return $this->selectFields($emptyFields, $productToSeed);

//        do {
//            $productToSeed = $this->getProductToSeed();
//            if (!$productToSeed) {
//                return false;
//            }
//            $emptyFields = $this->getEmptyFields();

//            $fieldsToSeed = $this->selectFields($emptyFields, $productToSeed);
//        } while (empty($fieldsToSeed));

//        return $fieldsToSeed;
//    }

    private function selectFields($fields, Product $product)
    {
        reset($fields);
        $index = key($fields);

        $finalFieldsAvailableToSeed = [];

        while (isset($fields[$index])) {
            $availableToSeed = true;

            for ($xIndex = 0; $xIndex < $product->getLength(); $xIndex++) {
                for ($yIndex = 0; $yIndex < $product->getHeight(); $yIndex++) {
                    $checkingIndex = $index + $xIndex + ($yIndex * 12);
                    if (!isset($fields[$checkingIndex]) || $this->isNextIndexInNextRow($index, $checkingIndex)) {
                        $availableToSeed = false;
                    }
                }
            }

            if (!$availableToSeed) {
                unset($fields[$index]);
            } else {
                $finalFieldsAvailableToSeed[$index] = clone $fields[$index];
                $indexesToRemove = $this->getIndexesToRemove($index, $product);
                foreach ($indexesToRemove as $indexToRemove) {
                    unset($fields[$indexToRemove]);
                }
                if ($product->getAmount() <= count($finalFieldsAvailableToSeed)) {
                    break;
                }
            }
            reset($fields);
            $index = key($fields);
        }

        /** @var Field $field */
        foreach ($finalFieldsAvailableToSeed as $field) {
            $field->setProduct($product);
        }

        return $finalFieldsAvailableToSeed;
    }

    private function getIndexesToRemove($currentIndex, Product $plant)
    {
        $indexes = [];

        for ($i = 0; $i < $plant->getLength(); $i++) {
            for ($j = 0; $j < $plant->getHeight(); $j++) {
                $indexToRemove = $currentIndex + $i + (12 * $j);
                $indexes[$indexToRemove] = $indexToRemove;
            }
        }

        return $indexes;
    }

    private function isNextIndexInNextRow($index, $nextIndex)
    {
        $currentColumn = $this->getColumn($index);
        $nextColumn = $this->getColumn($nextIndex);

        return $nextColumn < $currentColumn;
    }

    private function getColumn($index)
    {
        $column = $index % 12;
        if ($column == 0) {
            $column = 12;
        }

        return $column;
    }

    public function waterPlants(Farmland $farmland)
    {
        /** @var Field $field */
        foreach ($farmland->fields as $field) {
            if ($field->canWater()) {
                $this->connector->waterField($farmland, $field);
            }
        }
    }
}