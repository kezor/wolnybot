<?php
/**
 * Created by PhpStorm.
 * User: maciej
 * Date: 23.05.17
 * Time: 16:13
 */

namespace App\Service;


use App\Connector\WolniFarmerzyConnector;
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
                }
            }
        }
    }
}