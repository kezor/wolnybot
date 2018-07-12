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

    public function getGetFarmUrl()
    {
        return $this->getMainPart() . '&mode=getfarms&farm=1&position=0';
    }

    public function getGardenInitUrl(Farmland $farmland)
    {
        return $this->getMainPart() . '&mode=gardeninit&farm=' . $farmland->farm->id . '&position=' . $farmland->position;
    }

    public function getGardenHarvestUrl(Farmland $farmland, Field $field)
    {
        return $this->getMainPart() . '&mode=garden_harvest&farm=' . $farmland->farm->id . '&position=' . $farmland->position . '&pflanze[]=' . $field->getProduct()->getPid() . '&feld[]=' . $field->index . '&felder[]=' . $field->getRelatedFields();
    }

    public function getCropGardenUrl(Farmland $farmland)
    {
        return $this->getMainPart() . '&mode=cropgarden&farm=' . $farmland->farm->id . '&position=' . $farmland->position;
    }

    public function getGardenPlantUrl(Farmland $farmland, SingleBunchOfFields $singleBunchOfFields, Product $product)
    {
        return $this->getMainPart() . '&mode=garden_plant&farm=' . $farmland->farm->id . '&position=' . $farmland->position . $singleBunchOfFields->getUrlPartWithProduct($product);// '&pflanze[]=' . $product->getPid() . '&feld[]=' . $singleBunchOfFields->getIndexes() . '&felder[]=' . $singleBunchOfFields->getRelatedFields();
    }

    public function getGardenWaterUrl(Farmland $farmland, Field $field)
    {
        return $this->getMainPart() . '&mode=garden_water&farm=' . $farmland->farm->id . '&position=' . $farmland->position . '&feld[]=' . $field->index . '&felder[]=' . $field->getRelatedFields();
    }

    public function getFeedUrl(Hovel $hovel)
    {
        return $this->getMainPart() . '&mode=inner_feed&farm=' . $hovel->getFarmId() . '&position=' . $hovel->getPosition() . '&pid=1&c=1_1|&amount=1&guildjob=0';
    }

    public function getLoadHovelDataUrl(Hovel $hovel)
    {
        return $this->getMainPart() . '&mode=inner_init&farm=' . $hovel->getFarmId() . '&position=' . $hovel->getPosition();
    }

    public function getCollectEggsUrl(Hovel $hovel)
    {
        return $this->getMainPart() . '&mode=inner_crop&farm=' . $hovel->getFarmId() . '&position=' . $hovel->getPosition();
    }

    public function getFeedChickensUrl(Hovel $hovel, Product $plant)
    {
//        http://s1.wolnifarmerzy.pl/ajax/farm.php?rid=8f8e72c933f7ed67fd832e960743ad4b&mode=inner_feed&farm=1&position=2&pid=2&c=2_1|&amount=1&guildjob=0
//        http://s1.wolnifarmerzy.pl/ajax/farm.php?rid=8f8e72c933f7ed67fd832e960743ad4b&mode=inner_feed&farm=1&position=2&pid=1&c=1_1|&amount=1&guildjob=0
        return $this->getMainPart() . '&mode=inner_feed&farm=' . $hovel->getFarmId() . '&position=' . $hovel->getPosition() . '&pid=' . $plant->getPid() . '&c=1_1|&amount=1&guildjob=0';
    }

    private function getMainPart()
    {
        return 'http://s' . $this->player->server_id . '.wolnifarmerzy.pl/ajax/farm.php?rid=' . $this->token;
    }
}