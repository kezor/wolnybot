<?php

namespace Tests\Unit;

use App\Building\Hovel;
use App\SingleBunchOfFields;
use App\UrlGenerator;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class UrlGeneratorTest extends TestCase
{
    use DatabaseTransactions;

    public function testGetUrls()
    {
        $player = $this->getTestPlayer();
        $token = 'yghjurtdvbhytrfvbnrec';
        $product = $this->getTestProduct($player, 1); // wheat
        $field = $this->getTestField($product);

        $farm = $this->getTestFarm($player);

        $farmland = $this->getTestFarmland($farm);

        $hovel = new Hovel(['farm' => 1, 'position' => 2], $player);
        $url = new UrlGenerator($player, $token);

        $this->assertEquals(
            'http://s1.wolnifarmerzy.pl/ajax/farm.php?rid=yghjurtdvbhytrfvbnrec&mode=getfarms&farm=1&position=0',
            $url->getGetFarmUrl()
        );

        $this->assertEquals(
            'http://s1.wolnifarmerzy.pl/ajax/farm.php?rid=yghjurtdvbhytrfvbnrec&mode=gardeninit&farm=1&position=1',
            $url->getGardenInitUrl($farmland)
        );

        $this->assertEquals(
            'http://s1.wolnifarmerzy.pl/ajax/farm.php?rid=yghjurtdvbhytrfvbnrec&mode=garden_harvest&farm=1&position=1&pflanze[]=1&feld[]=1&felder[]=1,2',
            $url->getGardenHarvestUrl($farmland, $field)
        );

        $this->assertEquals(
            'http://s1.wolnifarmerzy.pl/ajax/farm.php?rid=yghjurtdvbhytrfvbnrec&mode=inner_feed&farm=1&position=2&pid=1&c=1_1|&amount=1&guildjob=0',
            $url->getFeedUrl($hovel)
        );

        $this->assertEquals(
            'http://s1.wolnifarmerzy.pl/ajax/farm.php?rid=yghjurtdvbhytrfvbnrec&mode=garden_water&farm=1&position=1&feld[]=1&felder[]=1,2',
            $url->getGardenWaterUrl($farmland, $field)
        );

        $this->assertEquals(
//                    http://s1.wolnifarmerzy.pl/ajax/farm.php?rid=8f8e72c 32e960743ad4b&mode=inner_init&farm=1&position=2
            'http://s1.wolnifarmerzy.pl/ajax/farm.php?rid=yghjurtdvbhytrfvbnrec&mode=inner_init&farm=1&position=2',
            $url->getLoadHovelDataUrl($hovel)
        );

        $this->assertEquals(
//                    http://s1.wolnifarmerzy.pl/ajax/farm.php?rid=8f8e72c 32e960743ad4b&mode=inner_init&farm=1&position=2
            'http://s1.wolnifarmerzy.pl/ajax/farm.php?rid=yghjurtdvbhytrfvbnrec&mode=inner_crop&farm=1&position=2',
            $url->getCollectEggsUrl($hovel)
        );

        $this->assertEquals(
//                    http://s1.wolnifarmerzy.pl/ajax/farm.php?rid=yghjurtdvbhytrfvbnrec&mode=inner_feed&farm=1&position=2&pid=1&c=1_1|&amount=1&guildjob=0
            'http://s1.wolnifarmerzy.pl/ajax/farm.php?rid=yghjurtdvbhytrfvbnrec&mode=inner_feed&farm=1&position=2&pid=1&c=1_1|&amount=1&guildjob=0',
            $url->getFeedChickensUrl($hovel, $product)
        );

        $this->assertEquals(
//            http://s1.wolnifarmerzy.pl/ajax/farm.php?rid=yghjurtdvbhytrfvbnrec&mode=cropgarden&farm=1&position=1
            'http://s1.wolnifarmerzy.pl/ajax/farm.php?rid=yghjurtdvbhytrfvbnrec&mode=cropgarden&farm=1&position=1',
            $url->getCropGardenUrl($farmland)
        );
    }

    public function testGardenPlantsUrlSinglePlant()
    {

        $player = $this->getTestPlayer();
        $token = 'yghjurtdvbhytrfvbnrec';
        $product = $this->getTestProduct($player, 1); // wheat
        $field = $this->getTestField($product);
        $farm = $this->getTestFarm($player);

        $farmland = $this->getTestFarmland($farm);

        $singleBunchOfFields = new SingleBunchOfFields();
        $singleBunchOfFields->push($field);

        $url = new UrlGenerator($player, $token);

        $this->assertEquals(
            'http://s1.wolnifarmerzy.pl/ajax/farm.php?rid=yghjurtdvbhytrfvbnrec&mode=garden_plant&farm=1&position=1&pflanze[]=1&feld[]=1&felder[]=1,2',
            $url->getGardenPlantUrl($farmland, $singleBunchOfFields, $product)
        );
    }

    public function testGardenPlantsUrlFewPlantsCarrot()
    {

        $player = $this->getTestPlayer();
        $token = 'yghjurtdvbhytrfvbnrec';
        $product = $this->getTestProduct($player, 17); // carrot

        $farm = $this->getTestFarm($player);

        $farmland = $this->getTestFarmland($farm);

        $singleBunchOfFields = new SingleBunchOfFields();

        $fieldsIndexes = [36, 48, 60, 72];

        foreach ($fieldsIndexes as $index){
            $field = $this->getTestField($product, ['index' => $index]);
            $singleBunchOfFields->push($field);
        }

        $url = new UrlGenerator($player, $token);

        $this->assertEquals(
            'http://s1.wolnifarmerzy.pl/ajax/farm.php?rid=yghjurtdvbhytrfvbnrec&mode=garden_plant&farm=1&position=1&pflanze[]=17&feld[]=36&felder[]=36&pflanze[]=17&feld[]=48&felder[]=48&pflanze[]=17&feld[]=60&felder[]=60&pflanze[]=17&feld[]=72&felder[]=72',
            $url->getGardenPlantUrl($farmland, $singleBunchOfFields, $product)
        );
    }

    public function testGardenPlantsUrlFewPlantsWheat()
    {

        $player = $this->getTestPlayer();
        $token = 'yghjurtdvbhytrfvbnrec';
        $product = $this->getTestProduct($player, 1); // carrot

        $farm = $this->getTestFarm($player);

        $farmland = $this->getTestFarmland($farm);

        $singleBunchOfFields = new SingleBunchOfFields();

        $fieldsIndexes = [49, 61, 73, 85, 97];

        foreach ($fieldsIndexes as $index){
            $field = $this->getTestField($product, ['index' => $index]);
            $singleBunchOfFields->push($field);
        }

        $url = new UrlGenerator($player, $token);

        $this->assertEquals(
            'http://s1.wolnifarmerzy.pl/ajax/farm.php?rid=yghjurtdvbhytrfvbnrec&mode=garden_plant&farm=1&position=1&pflanze[]=1&feld[]=49&felder[]=49,50&pflanze[]=1&feld[]=61&felder[]=61,62&pflanze[]=1&feld[]=73&felder[]=73,74&pflanze[]=1&feld[]=85&felder[]=85,86&pflanze[]=1&feld[]=97&felder[]=97,98',
            $url->getGardenPlantUrl($farmland, $singleBunchOfFields, $product)
        );
    }
}
