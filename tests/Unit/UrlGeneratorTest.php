<?php

namespace Tests\Unit;

use App\Building\Hovel;
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
            'http://s1.wolnifarmerzy.pl/ajax/farm.php?rid=yghjurtdvbhytrfvbnrec&mode=garden_plant&farm=1&position=1&pflanze[]=1&feld[]=1&felder[]=1,2',
            $url->getGardenPlantUrl($farmland, $field)
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
}
