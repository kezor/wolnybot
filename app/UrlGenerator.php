<?php

namespace App;


class UrlGenerator
{
    /**
     * @var Player
     */
    private $player;

    /**
     * @var string
     */
    private $token;

    public function __construct(Player $player, $token)
    {
        $this->player = $player;
        $this->token = $token;
    }

    public function getDashboardDataUrl()
    {
        return $this->getMainPart() . '&mode=getfarms&farm=1&position=0';
    }

    public function getSpaceFieldsUrl(Space $space)
    {
        return $this->getMainPart() . '&mode=gardeninit&farm=1&position=' . $space->position;
    }

    public function getCollectUrl(Field $field)
    {
        return $this->getMainPart() . '&mode=garden_harvest&farm=1&position='.$field->getSpace()->getPosition().'&pflanze[]=' . $field->getProduct()->getPid() . '&feld[]=' . $field->index . '&felder[]=' . $field->getFields();
    }

    public function getSeedUrl(Field $field)
    {
        return $this->getMainPart() . '&mode=garden_plant&farm='.$field->getSpace()->getFarm().'&position='.$field->getSpace()->getPosition().'&pflanze[]=' . $field->getProduct()->getPid() . '&feld[]=' . $field->index . '&felder[]=' . $field->getFields();
    }

    private function getMainPart()
    {
        return 'http://s' . $this->player->server_id . '.wolnifarmerzy.pl/ajax/farm.php?rid=' . $this->token;
    }
}