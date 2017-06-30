<?php

namespace App\Connector;


use App\Building\Farmland;
use App\Field;
use App\Player;
use App\Space;

interface ConnectorInterface
{
    public function login(Player $player);

    public function getDashboardData();

    public function getSpaceFields(Farmland $farmland);

    public function collect(Field $field);

    public function seed(Field $field);

    public function waterField(Field $field);

    public function buyBuilding(Space $space, $building);

    public function getBuildingsOptions(Space $space);

    public function increaseTutorialStep();

    public function closeTutorial();
}