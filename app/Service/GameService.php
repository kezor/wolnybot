<?php
/**
 * Created by PhpStorm.
 * User: maciej
 * Date: 23.05.17
 * Time: 16:13
 */

namespace App\Service;


use App\Building\Farmland;
use App\Connector\WolniFarmerzyConnector;
use App\Field;
use App\Product\AbstractProduct;
use App\Product\Carrot;
use App\Product\Cucumber;
use App\Product\Strawberry;
use App\Product\Wheat;
use App\Player;
use App\ProductCategoryMapper;
use App\ProductFactory;
use App\ProductMapper;
use App\Repository\FieldRepository;
use App\Repository\SpaceRepository;
use App\Repository\ProductRepository;
use App\Space;
use App\Product;
use Illuminate\Database\Eloquent\Collection;

class GameService
{
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

    /**
     * @var SpaceRepository
     */
    private $spaceRepository;

    /**
     * @var FieldRepository
     */
    private $fieldRepository;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    public function __construct(Player $player)
    {
        $this->connector = new WolniFarmerzyConnector();
        $this->player = $player;
        $this->connector->login($player);
        $this->spaceRepository = new SpaceRepository();
        $this->fieldRepository = new FieldRepository();
        $this->productRepository = new ProductRepository();
    }

    public function updateFields()
    {
        $dashboardData = $this->connector->getDashboardData();

        $farms = $dashboardData['updateblock']['farms']['farms'];

        foreach ($farms as $farm) {
            foreach ($farm as $spaceData) {
                if ($spaceData['status'] == 1 && $spaceData['buildingid'] == 1) { // @TODO temporary only for plant spaces
                    $space = $this->spaceRepository->getSpace($spaceData, $this->player);
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
                        $field = $this->fieldRepository->getField($fieldData, $space);
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

        $plants = $this->convertPlantsToSeed($fields);

        /** @var AbstractProduct $field */
        foreach ($plants as $plant) {
            $this->connector->collect($plant);
            $plant->setAsEmpty();
        }
    }

    /**
     * @param Field[] $fields
     */
    private function drawFieldsToCollect($fields)
    {
        for ($i = 1; $i <= 120; $i++) {
            echo '[' . (isset($fields[$i]) ? $i : 'O') . ']';
            if ($i % 12 == 0) {
                echo PHP_EOL;
            }
        }
    }

    private function convertPlantsToSeed($fields)
    {
        $plants = [];

        /** @var Field $field */
        foreach ($fields as $field) {
            $plants[] = ProductFactory::getProductFromField($field);
        }

        return $plants;
    }

    public function updateStock()
    {
        $dashboardData = $this->connector->getDashboardData();

        //update stock
        $stocks = $dashboardData['updateblock']['stock']['stock'];

        $updatedItemsInStock = [];

        foreach ($stocks as $product) {
            foreach ($product as $level1) {
                foreach ($level1 as $level2) {
                    /** @var Product $product */
                    $product = $this->productRepository->getStock($level2, $this->player);
                    $product->amount = $level2['amount'];
                    $product->duration = $level2['duration'];
                    $product->size = $level2['duration'];
                    $product->save();
                    $updatedItemsInStock[] = $product->id;
                    echo 'plant id: ' . $product->pid . ', amount: ' . $product->amount . PHP_EOL;
                }
            }
        }

        $emptyItemsInStock = $this->productRepository->getEmptyItems($updatedItemsInStock, $this->player);

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
            while ($this->isPossibleToSeed($space)) {
                $seeds = $this->getSeedToSeed($space);

                foreach ($seeds as $seed) {
                    $this->connector->seed($seed);
                }

                $this->updateStock();
                $this->updateFields();
            }
//            foreach ($fieldsToSeed as $field) {
//                $this->connector->watered($field);
//            }
        }
    }

    /**
     * @param Space $space
     * @return array
     */
    private function getSeedToSeed(Space $space)
    {
        /** @var Product $seedFromStock */
        $seedFromStock = Product::where('player', $this->player->id)
            ->where('amount', '>', 0)
            ->whereIn('pid', ProductCategoryMapper::getVegetablesPids())
            ->whereNotIn('pid', $this->usedSeeds)
            ->orderBy('amount', 'ASC')
            ->first();
        if (!$seedFromStock) {
            return [];
        }

        $this->usedSeeds[] = $seedFromStock->pid;

        echo 'Try to seed ' . $seedFromStock->id . PHP_EOL;


        $fieldsToSeed = $this->getFieldsToSeed($space, ProductFactory::getProductFromPid($seedFromStock->pid));

        $seeds = [];
        foreach ($fieldsToSeed as $field) {
            /** @var AbstractProduct $seedToSeed */
            $seedToSeed = ProductFactory::getProductFromPid($seedFromStock->pid);
            $seedToSeed->setSize($seedFromStock->size);
            $seedToSeed->setField($field);
            $seedToSeed->setField($field);
            $seeds[] = $seedToSeed;
        }

        return $seeds;
    }

    private function isPossibleToSeed(Space $space)
    {
        return $this->haveEnoughSeeds() && $this->haveFreeFields($space);
    }


    private function haveEnoughSeeds()
    {
        /** @var Collection $availablePlants */
        $availablePlants = Product::where('amount', '>', 0)
            ->where('player', $this->player->id)
            ->whereNotIn('pid', $this->usedSeeds)
            ->get();
        return $availablePlants->isNotEmpty();
    }

    private function haveFreeFields(Space $space)
    {
        /** @var Collection $availablePlants */
        $fields = $fieldsToCollect = Field::whereNull('product_pid')
            ->where('space', $space->id)
            ->get();
        return $fields->isNotEmpty();

    }


    private function getFieldsToSeed(Space $space, AbstractProduct $plant)
    {
        $fieldsCollection = $fieldsToCollect = Field::whereNull('product_pid')
            ->where('space', $space->id)
            ->get();

        $fields = [];
        /** @var Field $field */
        foreach ($fieldsCollection as $field) {
            $fields[$field->index] = $field;
        }

        $this->drawFieldsToCollect($fields);

        reset($fields);
        $index = key($fields);

        $finalFieldsAvailableToSeed = [];

        while (isset($fields[$index])) {
            /** @var Field $field */
            $field = $fields[$index];
            $removeIndex = false;

            for ($xIndex = 1; $xIndex < $plant->getLength(); $xIndex++) {
                $nextIndex = $field->index + $xIndex;
                if (!isset($fields[$nextIndex]) || $this->isNextIndexInNextRow($index, $nextIndex)) {
                    $removeIndex = true;
                }
            }
            if (!$removeIndex) {
                for ($yIndex = 1; $yIndex <= $plant->getHeight(); $yIndex++) {
                    $nextIndex = $field->index + $yIndex + 11;
//                    var_dump('sprawdzam '. $index.' nastepny index '.$nextIndex);
                    if (!isset($fields[$nextIndex]) || $this->isNextIndexInNextRow($index, $nextIndex)) {
                        $removeIndex = true;
                    }
                }
            }
            if ($removeIndex) {
                unset($fields[$index]);
            } else {
                $finalFieldsAvailableToSeed[$index] = $fields[$index];
                $indexesToRemove = $this->getIndexesToRemove($index, $plant);
//                var_dump('sprawdzam ' . $index . 'Bede usuwal' . implode(',', array_values($indexesToRemove)));
                foreach ($indexesToRemove as $indexToRemove) {
                    unset($fields[$indexToRemove]);
                }
            }
            reset($fields);
            $index = key($fields);
        }
        return $finalFieldsAvailableToSeed;
    }

    private function getIndexesToRemove($currentIndex, AbstractProduct $plant)
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

    protected function getColumn($index)
    {
        $column = $index % 12;
        if ($column == 0) {
            $column = 12;
        }
        return $column;
    }

    protected function isNextIndexInNextRow($index, $nextIndex)
    {
        $currentColumn = $this->getColumn($index);
        $nextColumn = $this->getColumn($nextIndex);

        return $nextColumn < $currentColumn;
    }

    public function disableTutorial()
    {
        $space = new Space();
        $space->farm = 1;
        $space->position = 1;
        //OK http://s8.wolnifarmerzy.pl/ajax/farm.php?rid=fe3faac43740b3f28e6d6bba45c633cb&mode=getbuildingoptions&farm=1&position=1
        $this->connector->getGuildingsOptions($space);


        //OK http://s8.wolnifarmerzy.pl/ajax/farm.php?rid=fe3faac43740b3f28e6d6bba45c633cb&mode=buybuilding&farm=1&position=1&id=1&buildingid=1
        $farmland = new Farmland();
        $this->connector->buyBuilding($space, $farmland);

        //OK http://s8.wolnifarmerzy.pl/ajax/farm.php?rid=fe3faac43740b3f28e6d6bba45c633cb&mode=gardeninit&farm=1&position=1
        $this->connector->getSpaceFields($space);

        //http://s8.wolnifarmerzy.pl/ajax/farm.php?rid=fe3faac43740b3f28e6d6bba45c633cb&mode=garden_plant&farm=1&position=1&pflanze[]=17&feld[]=3&felder[]=3&cid=12
        $firstField = new Field();
        $firstField->index = 1;
        $carrot = new Carrot($firstField);
        $this->connector->seed($carrot->getPid());

        //http://s8.wolnifarmerzy.pl/ajax/farm.php?rid=fe3faac43740b3f28e6d6bba45c633cb&mode=garden_water&farm=1&position=1&feld[]=3&felder[]=3
        $this->connector->watered($firstField);

        //wait 15-20 sec
        echo 'Sleep for 20 seconds' . PHP_EOL;
        for ($i = 0; $i < 20; $i++) {
            echo 20 - $i . ' ';
            sleep(1);
        }
        echo PHP_EOL;
        //http://s8.wolnifarmerzy.pl/ajax/farm.php?rid=fe3faac43740b3f28e6d6bba45c633cb&mode=garden_harvest&farm=1&position=1&pflanze[]=17&feld[]=3&felder[]=3
        $this->connector->collect($carrot);

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