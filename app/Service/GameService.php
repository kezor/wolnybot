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
     * @var Field[]
     */
    private $fields = [];

    /**
     * @var Player
     */
    private $player;

    public function __construct(Player $player)
    {
        $this->connector = new WolniFarmerzyConnector();
        $this->player    = $player;
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
                    $space           = $this->spaceRepository->getSpace($spaceData, $this->player);
                    $space->farm     = $spaceData['farm'];
                    $space->position = $spaceData['position'];
                    $space->save();
                    $this->spaces[] = $space;

                    if (!$space->isFieldsInDatabase()) {
                        for ($i = 1; $i <= 120; $i++) {
                            $field             = new Field();
                            $field->space      = $space->id;
                            $field->index      = $i;
                            $field->plant_type = Field::FIELD_EMPTY;
                            $field->offset_x   = 0;
                            $field->offset_y   = 0;
                            $field->save();
                        }
                        $space->fields_in_database = true;
                        $space->save();
                    }

                    $fieldsData = $this->connector->getSpaceFields($space);
                    $fields     = $fieldsData['datablock'][1];

                    if ($fields == 0) {
                        continue;
                    }
                    foreach ($fields as $key => $fieldData) {
                        if (!is_numeric($key)) {
                            continue;
                        }
                        $field             = $this->fieldRepository->getField($fieldData, $space);
                        $field->plant_type = $fieldData['inhalt'];
                        $field->offset_x   = $fieldData['x'];
                        $field->offset_y   = $fieldData['y'];
                        $field->planted    = $fieldData['gepflanzt'];
                        $field->time       = $fieldData['zeit'];
                        $field->save();

                        $this->fields[] = $field;

                    }
                }
            }
        }
    }

    public function collectReady()
    {
        // try to collect

        $fieldsToCollect = Field::where('offset_x', 1)
            ->where('offset_y', 1)
            ->where('time', '<', time())
            ->where('time', '!=', 0)
            ->get();

        echo "Ready to collect: " . count($fieldsToCollect) . PHP_EOL;

        /** @var Field $field */
        foreach ($fieldsToCollect as $field) {
            if ($field->canCollect()) {
                $this->connector->collect($field);
                $field->plant_type = Field::FIELD_EMPTY;
                $field->time       = 0;
                $field->planted    = 0;
                $field->save();
            }
        }
    }

    public function updateStock()
    {
        $dashboardData = $this->connector->getDashboardData();

        //update stock
        $stocks = $dashboardData['updateblock']['stock']['stock'];

        foreach ($stocks as $stock) {
            foreach ($stock as $level1) {
                foreach ($level1 as $level2) {
                    $stock = Stock::where('plant_pid', $level2['pid'])
                        ->first();
                    if (!$stock) {
                        $stock            = new Stock();
                        $stock->plant_pid = $level2['pid'];
                    }
                    $stock = $this->stockRepository->getStock($level2);
                    $stock->amount   = $level2['amount'];
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
            ->where('plant_pid', 17)//take only carrots
            ->first();

        $fieldsToSeed = $fieldsToCollect = Field::where('plant_type', Field::FIELD_EMPTY)
            ->limit($availablePlants->amount)
            ->get();

        foreach ($fieldsToSeed as $field) {
            $this->connector->seed($field, 17);
        }
    }
}