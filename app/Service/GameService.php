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
use App\Space;

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
        $this->player = $player;
        $this->connector->login($player);
    }

    public function update()
    {
        $dashboardData = $this->connector->getDashboardData();

        //first update farms
        $farms = $dashboardData['updateblock']['farms']['farms'];

        foreach ($farms as $farm) {
            foreach ($farm as $spaceData) {
                if ($spaceData['status'] == 1) {
                    $space = Space::where('player', $this->player->id)
                        ->where('farm', $spaceData['farm'])
                        ->where('position', $spaceData['position'])
                        ->first();
                    if (!$space) {
                        $space = new Space();
                    }
                    $space->player = $this->player->id;
                    $space->farm = $spaceData['farm'];
                    $space->position = $spaceData['position'];
                    $space->save();
                    $this->spaces[] = $space;

                    if (!$space->isFieldsInDatabase()) {
                        for ($i = 1; $i <= 120; $i++) {
                            $field = new Field();
                            $field->space = $space->id;
                            $field->index = $i;
                            $field->plant_type = Field::FIELD_UNKNOWN;
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
                    foreach ($fields as $key => $fieldData) {
                        if (!is_numeric($key)) {
                            continue;
                        }
                        $field = Field::where('space', $space->id)
                            ->where('index', $fieldData['teil_nr'])
                            ->first();
                        if (!$field) {
                            $field = new Field();
                        }
                        echo "Try to update some fucking fields: " . $field->id . PHP_EOL;
                        $field->plant_type = $fieldData['inhalt'];
                        $field->offset_x = $fieldData['x'];
                        $field->offset_y = $fieldData['y'];
                        $field->planted = $fieldData['gepflanzt'];
                        $field->time = $fieldData['zeit'];
                        $field->save();

                        $this->fields[] = $field;

                    }
                }
            }
        }

        // try to collect

        $fieldsToCollect = Field::where('offset_x', 1)
            ->where('offset_y', 1)
            ->where('time', '<', time())
            ->get();

        foreach ($fieldsToCollect as $field) {
            if (!in_array($field->plant_type, [
                Field::FIELD_COCKROACHES,
                Field::FIELD_WEEDS,
                Field::FIELD_STONES,
                Field::FIELD_STUMPS,
            ])
            ) {
                $this->connector->collect($field);
                $field->plant_type = Field::FIELD_EMPTY;
                $field->save();
            }
        }

        // try to seed

        $fieldsToSeed = $fieldsToCollect = Field::where('plant_type', Field::FIELD_EMPTY)
            ->orWhere('plant_type', Field::FIELD_UNKNOWN)
            ->get();
        foreach ($fieldsToSeed as $field) {
            $this->connector->seed($field, 17);
        }
    }
}