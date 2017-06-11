<?php

namespace App\Service;


use App\Building\Farmland;
use App\Connector\ConnectorInterface;
use App\Connector\WolniFarmerzyConnector;
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

    /**
     * @var Space[]
     */
    private $spaces = [];
    private $usedSeeds = [];

    /**
     * @var Player
     */
    private $player;

    public function __construct(Player $player, ConnectorInterface $connector)
    {
        $this->connector = $connector;
        $this->player = $player;
        $this->connector->login($player);
    }

    public function updateFields()
    {
        $dashboardData = $this->connector->getDashboardData();

        $farms = $dashboardData['updateblock']['farms']['farms'];

        foreach ($farms as $farm) {
            foreach ($farm as $spaceData) {
                if ($spaceData['status'] == 1 && $spaceData['buildingid'] == 1) { // @TODO temporary only for plant spaces
                    $space = SpaceRepository::getSpace($spaceData, $this->player);
                    $space->building_type = $spaceData['buildingid'];
                    $space->save();
                    $this->spaces[] = $space;

                    if (!$space->isFieldsInDatabase()) {
                        $this->fillDatabaseWithEmptyFields($space);
                    }

                    $fieldsData = $this->connector->getSpaceFields($space);
                    $fields = $fieldsData['datablock'][1];

                    if ($fields == 0) {
                        continue;
                    }
                    $updatedFieldIds = [];
                    foreach ($fields as $key => $fieldData) {
                        if (!is_numeric($key)) {
                            continue;
                        }
                        $field = FieldRepository::getField($fieldData, $space);
                        $field->product_pid = $fieldData['inhalt'];
                        $field->offset_x = $fieldData['x'];
                        $field->offset_y = $fieldData['y'];
                        $field->phase = $fieldData['phase'];
                        $field->planted = $fieldData['gepflanzt'];
                        $field->time = $fieldData['zeit'];
                        $field->save();
                        $updatedFieldIds[] = $field->index;
                    }

                    $values = range(1, 120);
                    $restFieldI0ds = array_combine($values, $values);

                    $restFieldI0ds = array_diff($restFieldI0ds, $updatedFieldIds);

                    $festFields = Field::whereIn('index', $restFieldI0ds)
                        ->where('space', $space->id)
                        ->get();
                    /** @var Field $field */
                    foreach ($festFields as $field) {
                        $field->product_pid = null;
                        $field->offset_x = 0;
                        $field->offset_y = 0;
                        $field->planted = 0;
                        $field->time = 0;
                        $field->save();
                    }
                }
            }
        }
    }

    public function drawSpace(Space $space)
    {
        /**
         * @var integer $key
         * @var Field $field
         */
        foreach ($space->getFields() as $key => $field) {
            echo $field->drawField();
            if ((($key + 1) % 12) == 0) {
                echo PHP_EOL;
            }
        }
    }

    private function fillDatabaseWithEmptyFields(Space $space)
    {
        for ($i = 1; $i <= 120; $i++) {
            $field = new Field();
            $field->space = $space->id;
            $field->index = $i;
            $field->product_pid = null;
            $field->offset_x = 0;
            $field->offset_y = 0;
            $field->phase = 4;
            $field->save();
        }
        $space->fields_in_database = true;
        $space->save();
    }

    public function collectReady()
    {
        $spaces = $this->player->getSpaces();
        /** @var Space $space */
        foreach ($spaces as $space) {
            $this->collectFromSpace($space);
        }
    }

    private function collectFromSpace(Space $space)
    {
        $fieldsToCollect = $space->getFieldsToCollect();

        $fields = [];
        /** @var Field $item */
        foreach ($fieldsToCollect as $item) {
            $fields[$item->index] = $item;
        }

        /** @var Field $finalFieldToReset */
        foreach ($fields as $key => &$finalFieldToReset) {
            for ($i = 0; $i < $finalFieldToReset->offset_x; $i++) {
                for ($j = 0; $j < $finalFieldToReset->offset_y; $j++) {
                    $indexToRemove = $i + $finalFieldToReset->index + ($j * 12);
                    if ($indexToRemove !== $finalFieldToReset->index) {
                        unset($fields[$indexToRemove]);
                    }
                }
            }
        }

        /** @var Field $field */
        foreach ($fields as $field) {
            $this->connector->collect($field);
            $field->setAsEmpty();
        }
        echo 'Collected ' . count($fields) . ' on space position: ' . $space->position . PHP_EOL;
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
                    $product->duration = $level2['duration'];
                    $product->size = $level2['duration'];
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

    public function seed()
    {
        $userSpaces = $this->player->getSpaces();
        foreach ($userSpaces as $space) {
            echo 'working with space: ' . $space->position . PHP_EOL;
            $this->updateStock();
            $this->updateFields();

            $seededFields = [];

            $fieldsToSeed = $this->getFieldsToSeed($space);
            while (!empty($fieldsToSeed)) {
                reset($fieldsToSeed);
                $index = key($fieldsToSeed);
                /** @var Field[] $fieldsToSeed */
                echo 'Found ' . count($fieldsToSeed) . ' fields available to seed ' . $fieldsToSeed[$index]->getProduct()->getPid() . PHP_EOL;
                foreach ($fieldsToSeed as $field) {
                    $result = $this->connector->seed($field);

//                    if($result){
                    $seededFields[] = $field;
//                    }
                }

                $this->updateStock();
                $this->updateFields();

                $fieldsToSeed = $this->getFieldsToSeed($space);
            }
            $this->waterFields($seededFields);
        }
    }

    private function waterFields($fields)
    {
        /** @var Field $field */
        foreach ($fields as $field) {
            $this->connector->waterField($field);
        }
    }

    private function getFieldsToSeed(Space $space)
    {
        do {
            $productToSeed = $this->getProductToSeed();
            if (!$productToSeed) {
                return false;
            }
            $emptyFields = $this->getEmptyFields($space);

            $fieldsToSeed = $this->selectFields($emptyFields, $productToSeed);
        } while (empty($fieldsToSeed));

        return $fieldsToSeed;
    }

    private function getEmptyFields(Space $space)
    {
        return Field::whereNull('product_pid')
            ->where('space', $space->id)
            ->get();
    }

    private function getProductToSeed()
    {
        /** @var Product $stockProduct */
        $stockProduct = Product::where('player', $this->player->id)
            ->where('amount', '>', 0)
            ->whereIn('pid', ProductCategoryMapper::getVegetablesPids())
            ->whereNotIn('pid', $this->usedSeeds)
            ->orderBy('amount', 'ASC')
            ->first();
        if (!$stockProduct) {
            return null;
        }

        $this->usedSeeds[] = $stockProduct->pid;

        return $stockProduct;
    }

    private function selectFields($fieldsCollection, Product $product)
    {
        $fields = [];
        /** @var Field $field */
        foreach ($fieldsCollection as $field) {
            $fields[$field->index] = $field;
        }

        reset($fields);
        $index = key($fields);

        $finalFieldsAvailableToSeed = [];

        while (isset($fields[$index])) {
            $availableToSeed = true;

            for ($xIndex = 0; $xIndex < $product->getLength(); $xIndex++) {
                for ($yIndex = 0; $yIndex < $product->getHeight(); $yIndex++) {
                    $checkingIndex = $index + $xIndex + ($yIndex * 12);
//                    echo 'current index '.$index.' checking index '.$checkingIndex.PHP_EOL;
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

    protected function isNextIndexInNextRow($index, $nextIndex)
    {
        $currentColumn = $this->getColumn($index);
        $nextColumn = $this->getColumn($nextIndex);

        return $nextColumn < $currentColumn;
    }

    protected function getColumn($index)
    {
        $column = $index % 12;
        if ($column == 0) {
            $column = 12;
        }

        return $column;
    }

    public function disableTutorial()
    {
        $space = new Space();
        $space->farm = 1;
        $space->position = 1;
        //OK http://s8.wolnifarmerzy.pl/ajax/farm.php?rid=fe3faac43740b3f28e6d6bba45c633cb&mode=getbuildingoptions&farm=1&position=1
        $this->connector->getBuildingsOptions($space);


        //OK http://s8.wolnifarmerzy.pl/ajax/farm.php?rid=fe3faac43740b3f28e6d6bba45c633cb&mode=buybuilding&farm=1&position=1&id=1&buildingid=1
        $farmland = new Farmland();
        $this->connector->buyBuilding($space, $farmland);

        //OK http://s8.wolnifarmerzy.pl/ajax/farm.php?rid=fe3faac43740b3f28e6d6bba45c633cb&mode=gardeninit&farm=1&position=1
        $this->connector->getSpaceFields($space);

        //http://s8.wolnifarmerzy.pl/ajax/farm.php?rid=fe3faac43740b3f28e6d6bba45c633cb&mode=garden_plant&farm=1&position=1&pflanze[]=17&feld[]=3&felder[]=3&cid=12
        $firstField = new Field();
        $firstField->index = 1;
        $carrot = new Product($firstField);
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