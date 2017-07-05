<?php

namespace App;


use App\Building\Farmland;
use App\Building\Hovel;

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

    public function getSpaceFieldsUrl(Farmland $farmland)
    {
        return $this->getMainPart() . '&mode=gardeninit&farm=' . $farmland->getFarmId() . '&position=' . $farmland->getPosition();
    }

    public function getCollectUrl(Farmland $farmland, Field $field)
    {
        return $this->getMainPart() . '&mode=garden_harvest&farm=' . $farmland->getFarmId() . '&position=' . $farmland->getPosition() . '&pflanze[]=' . $field->getProduct()->getPid() . '&feld[]=' . $field->getIndex() . '&felder[]=' . $field->getRelatedFields();
    }

    public function getSeedUrl(Farmland $farmland, Field $field)
    {
        return $this->getMainPart() . '&mode=garden_plant&farm=' . $farmland->getFarmId() . '&position=' . $farmland->getPosition() . '&pflanze[]=' . $field->getProduct()->getPid() . '&feld[]=' . $field->getIndex() . '&felder[]=' . $field->getRelatedFields();
    }

    public function getWaterUrl(Farmland $farmland, Field $field)
    {
        return $this->getMainPart() . '&mode=garden_water&farm=' . $farmland->getFarmId() . '&position=' . $farmland->getPosition() . '&feld[]=' . $field->getIndex() . '&felder[]=' . $field->getRelatedFields();
    }

    public function getFeedUrl(Hovel $hovel)
    {
        return $this->getMainPart() . '&mode=inner_feed&farm=' . $hovel->getFarmId() . '&position=' . $hovel->getPosition() . '&pid=1&c=1_1|&amount=1&guildjob=0';
    }

    public function getLoadHovelData(Hovel $hovel)
    {
        return $this->getMainPart() . '&mode=inner_init&farm=' . $hovel->getFarmId() . '&position=' . $hovel->getPosition();
    }

    private function getMainPart()
    {
        return 'http://s' . $this->player->server_id . '.wolnifarmerzy.pl/ajax/farm.php?rid=' . $this->token;
    }
}