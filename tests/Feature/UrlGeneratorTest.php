<?php

namespace Tests\Feature;

use App\Field;
use App\Player;
use App\Product\AbstractProduct;
use App\Product\Carrot;
use App\Product\Corn;
use App\Space;
use App\UrlGenerator;
use Tests\TestCase;

class UrlGeneratorTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testGetUrls()
    {
        $player = $this->getTestPlayer();
        $token = 'yghjurtdvbhytrfvbnrec';
        $space = $this->getTestSpace($player);
        $product = $this->getTestProduct();
        $field = $this->getTestField($product, $space);


        $url = new UrlGenerator($player, $token);

        $this->assertEquals(
            'http://s1.wolnifarmerzy.pl/ajax/farm.php?rid=yghjurtdvbhytrfvbnrec&mode=getfarms&farm=1&position=0',
            $url->getDashboardDataUrl()
        );


        $this->assertEquals(
            'http://s1.wolnifarmerzy.pl/ajax/farm.php?rid=yghjurtdvbhytrfvbnrec&mode=gardeninit&farm=1&position=1',
            $url->getSpaceFieldsUrl($space)
        );

        $this->assertEquals(
            'http://s1.wolnifarmerzy.pl/ajax/farm.php?rid=yghjurtdvbhytrfvbnrec&mode=garden_harvest&farm=1&position=1&pflanze[]=1&feld[]=1&felder[]=1,13,2,14',
            $url->getCollectUrl($field)
        );

        $this->assertEquals(
            'http://s1.wolnifarmerzy.pl/ajax/farm.php?rid=yghjurtdvbhytrfvbnrec&mode=garden_plant&farm=1&position=1&pflanze[]=1&feld[]=1&felder[]=1,13,2,14',
            $url->getSeedUrl($field)
        );
    }
}
