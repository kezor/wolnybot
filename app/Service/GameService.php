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
                        $space->player = $this->player->id;
                        $space->farm = $spaceData['farm'];
                        $space->position = $spaceData['position'];
                        $space->save();
                    }
                    $this->spaces[] = $space;

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
                            ->where('position_id', $fieldData['teil_nr'])
                            ->first();
                        if (!$field) {
                            $field = new Field();
                            $field->space = $space->id;
                            $field->position_id = $fieldData['teil_nr'];
                            $field->plant_type = $fieldData['inhalt'];
                            $field->offset_x = $fieldData['x'];
                            $field->offset_y = $fieldData['y'];
                            $field->planted = $fieldData['gepflanzt'];
                            $field->time = $fieldData['zeit'];
                            $field->save();
                        }
                    }
                }
            }
        }

        // try to collect everything
    }
}