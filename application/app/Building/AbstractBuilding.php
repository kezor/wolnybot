<?php

namespace App\Building;


use App\Connector\ConnectorInterface;
use App\Connector\WolniFarmerzyConnector;
use App\Player;

abstract class AbstractBuilding
{
    protected $position;

    protected $farmId;

    /**
     * @var ConnectorInterface
     */
    protected $connector;

    /**
     * @var Player
     */
    protected $player;

    public function __construct($spaceData, $player)
    {
        $this->position = $spaceData['position'];
        $this->farmId = $spaceData['farm'];

        $this->player = $player;

        $this->connector = new WolniFarmerzyConnector();
    }

    abstract public function process();

    public function getPosition()
    {
        return $this->position;
    }

    public function getFarmId()
    {
        return $this->farmId;
    }

    public function setConnector(ConnectorInterface $connector)
    {
        $this->connector = $connector;
    }
}