<?php

namespace App\Connector;


use App\Building\Farmland;
use App\Field;
use App\Player;
use App\SingleBunchOfFields;
use App\Space;

interface ConnectorInterface
{
    public function login(Player $player);

    public function getDashboardData();

    public function cropGarden(Farmland $farmland);

    public function getFarmlandFields(Farmland $farmland);

    public function collect(Farmland $farmland, Field $field);

    public function seed(Farmland $farmland, SingleBunchOfFields $singleBunchOfFields);

    public function waterField(Farmland $farmland, Field $field);

    public function buyBuilding(Space $space, $building);

    public function getBuildingsOptions(Space $space);

    public function increaseTutorialStep();

    public function closeTutorial();
}