<?php
/**
 * Created by PhpStorm.
 * User: maciej
 * Date: 23.05.17
 * Time: 16:13
 */

namespace App\Service;


use App\Connector\WolniFarmerzyConnector;
use App\Field;
use App\Plant\AbstractPlant;
use App\Plant\Carrot;
use App\Plant\Wheat;
use App\Player;
use App\Repository\FieldRepository;
use App\Repository\SpaceRepository;
use App\Repository\StockRepository;
use App\Space;
use App\Stock;

class GameService
{
    private $connector;

    /**
     * @var Space[]
     */
    private $spaces = [];

    /**
     * @var Player
     */
    private $player;

    public function __construct(Player $player)
    {
        $this->connector = new WolniFarmerzyConnector();
        $this->player = $player;
        $this->connector->login($player);
        $this->spaceRepository = new SpaceRepository();
        $this->fieldRepository = new FieldRepository();
        $this->stockRepository = new StockRepository();
    }

    public function updateFields()
    {
        $dashboardData = $this->connector->getDashboardData();

        //first update farms
        $farms = $dashboardData['updateblock']['farms']['farms'];

        foreach ($farms as $farm) {
            foreach ($farm as $spaceData) {
                if ($spaceData['status'] == 1 && $spaceData['buildingid'] != 0) {
                    $space = $this->spaceRepository->getSpace($spaceData, $this->player);
                    $space->farm = $spaceData['farm'];
                    $space->position = $spaceData['position'];
                    $space->save();
                    $this->spaces[] = $space;

                    if (!$space->isFieldsInDatabase()) {
                        for ($i = 1; $i <= 120; $i++) {
                            $field = new Field();
                            $field->space = $space->id;
                            $field->index = $i;
                            $field->plant_type = Field::FIELD_EMPTY;
                            $field->offset_x = 0;
                            $field->offset_y = 0;
                            $field->save();
                        }
                        $space->fields_in_database = true;
                        $space->save();
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
                        $field->plant_type = $fieldData['inhalt'];
                        $field->offset_x = $fieldData['x'];
                        $field->offset_y = $fieldData['y'];
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
                    foreach ($festFields as $field) {
                        $field->plant_type = Field::FIELD_EMPTY;
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

    public function collectReady()
    {
        // try to collect

        $fieldsToCollect = Field::where('time', '<', time())
            ->where('time', '!=', 0)
            ->get();

        $fields = [];
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

        echo "Ready to collect: " . count($fields) . ' fields.' . PHP_EOL;

        $plants = $this->convertFieldToPlants($fields);

        /** @var AbstractPlant $field */
        foreach ($plants as $plant) {
            if ($plant->canCollect()) {
                $this->connector->collect($plant);
                $plant->setAsEmpty();
            }
        }
    }

    private function convertFieldToPlants($fields)
    {
        $plants = [];

        /** @var Field $field */
        foreach ($fields as $field) {
            switch ($field->plant_type) {
                case 17: //carrot
                    $plants[] = new Carrot($field);
                    break;
                case 1: //wheat
                    $plants[] = new Wheat($field);
                    break;
            }

        }
        return $plants;
    }

    public function updateStock()
    {
        $dashboardData = $this->connector->getDashboardData();

        //update stock
        $stocks = $dashboardData['updateblock']['stock']['stock'];

        foreach ($stocks as $stock) {
            foreach ($stock as $level1) {
                foreach ($level1 as $level2) {
                    $stock = $this->stockRepository->getStock($level2, $this->player);
                    $stock->amount = $level2['amount'];
                    $stock->duration = $level2['duration'];
                    $stock->save();
                }
            }
        }
    }

    public function seed()
    {
        // try to seed

        $availablePlants = Stock::where('amount', '>', 0)
            ->where('player', $this->player->id)
            ->where('plant_pid', 17)//take only carrots
            ->first();

        if (!$availablePlants) {
            echo 'There is no available plants' . PHP_EOL;
            return;
        }

        echo "Available carrot seeds: " . $availablePlants->amount . PHP_EOL;

        $userSpaces = Space::where('player', $this->player->id)
            ->get();

        if (!$userSpaces) {
            echo 'There is no available spaces for user' . PHP_EOL;
            return;
        }

        foreach ($userSpaces as $userSpace) {
            $fieldsToSeed = $fieldsToCollect = Field::where('plant_type', Field::FIELD_EMPTY)
                ->where('space', $userSpace->id)
                ->limit($availablePlants->amount)
                ->get();

            if (!$fieldsToSeed) {
                echo 'There is no available fields for this space' . PHP_EOL;
                return;
            }

            echo "Available fields to seeds: " . count($fieldsToSeed) . PHP_EOL;

            foreach ($fieldsToSeed as $field) {
                $this->connector->seed($field, 17);
            }
        }

    }

    public function disableTutorial()
    {
        // first step tutorial
        $this->connector->increaseTutorialStep();
        $this->connector->disableTutorial();

        //second step tutorial
        for($i=0; $i<3; $i++){
            $this->connector->increaseTutorialStep();
        }

        $this->connector->disableTutorial();
        $this->player->active = true;
        $this->player->save();
    }
}