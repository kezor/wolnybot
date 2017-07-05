<?php

namespace Tests\Feature;

use App\Building\Farmland;
use App\Building\Hovel;
use App\BuildingType;
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

        $farmland = new Farmland(['farm' => 1, 'position' => 1], $player);

        $hovel = new Hovel(['farm' => 1, 'position' => 2], $player);
        $url = new UrlGenerator($player, $token);

        $this->assertEquals(
            'http://s1.wolnifarmerzy.pl/ajax/farm.php?rid=yghjurtdvbhytrfvbnrec&mode=getfarms&farm=1&position=0',
            $url->getDashboardDataUrl()
        );


        $this->assertEquals(
            'http://s1.wolnifarmerzy.pl/ajax/farm.php?rid=yghjurtdvbhytrfvbnrec&mode=gardeninit&farm=1&position=1',
            $url->getSpaceFieldsUrl($farmland)
        );

        $this->assertEquals(
            'http://s1.wolnifarmerzy.pl/ajax/farm.php?rid=yghjurtdvbhytrfvbnrec&mode=garden_harvest&farm=1&position=1&pflanze[]=1&feld[]=1&felder[]=1,2',
            $url->getCollectUrl($farmland, $field)
        );

        $this->assertEquals(
            'http://s1.wolnifarmerzy.pl/ajax/farm.php?rid=yghjurtdvbhytrfvbnrec&mode=garden_plant&farm=1&position=1&pflanze[]=1&feld[]=1&felder[]=1,2',
            $url->getSeedUrl($farmland, $field)
        );

        $this->assertEquals(
            'http://s1.wolnifarmerzy.pl/ajax/farm.php?rid=yghjurtdvbhytrfvbnrec&mode=inner_init&farm=1&position=2',
            $url->getLoadHovelData($hovel)
        );

        $this->assertEquals(
            'http://s1.wolnifarmerzy.pl/ajax/farm.php?rid=yghjurtdvbhytrfvbnrec&mode=inner_feed&farm=1&position=2&pid=1&c=1_1|&amount=1&guildjob=0',
            $url->getFeedUrl($hovel)
        );

        $this->assertEquals(
            'http://s1.wolnifarmerzy.pl/ajax/farm.php?rid=yghjurtdvbhytrfvbnrec&mode=garden_water&farm=1&position=1&feld[]=1&felder[]=1,2',
            $url->getWaterUrl($farmland, $field)
        );

    }
}
