<?php

namespace App\Connector;


use App\Field;
use App\Player;
use App\Space;

interface ConnectorInterface
{
    public function login(Player $player);

    public function getDashboardData();

    public function getSpaceFields(Space $space);

    public function collect(Field $field);

    public function seed(Field $field);

    public function waterField(Field $field);

    public function buyBuilding(Space $space, $building);

    public function getBuildingsOptions(Space $space);

    public function increaseTutorialStep();

    public function closeTutorial();
}