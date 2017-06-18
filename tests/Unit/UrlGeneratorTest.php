<?php

namespace Tests\Unit;

use App\Field;
use App\Player;
use App\Product\AbstractProduct;
use App\Product\Carrot;
use App\Product\Corn;
use App\Space;
use App\UrlGenerator;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class UrlGeneratorTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @dataProvider getDataToTests
     */
    public function testGetUrlsForFarmlandFirstFarmFirstSpace($spacePosition, $dashboardUrl, $gardenInitUrl, $productToCollect, $collectUrl, $productToSeed, $seedUrl)
    {
        $player = $this->getTestPlayer();
        $token = 'yghjurtdvbhytrfvbnrec';
        $space = $this->getTestSpace($player, $spacePosition);

        $collectProduct = $this->getTestProduct($productToCollect);
        $collectField = $this->getTestField($collectProduct, $space);

        $seedProduct = $this->getTestProduct($productToSeed);
        $seedField = $this->getTestField($seedProduct, $space);

        $url = new UrlGenerator($player, $token);
        $this->assertEquals($dashboardUrl, $url->getDashboardDataUrl());
        $this->assertEquals($gardenInitUrl, $url->getSpaceFieldsUrl($space));
        $this->assertEquals($collectUrl, $url->getCollectUrl($collectField));
        $this->assertEquals($seedUrl, $url->getSeedUrl($seedField));
    }

    public function getDataToTests()
    {
        return [
            [
                'spacePosition' => 1,
                'dashboardUrl' => 'http://s1.wolnifarmerzy.pl/ajax/farm.php?rid=yghjurtdvbhytrfvbnrec&mode=getfarms&farm=1&position=0',
                'gardenInitUrl' => 'http://s1.wolnifarmerzy.pl/ajax/farm.php?rid=yghjurtdvbhytrfvbnrec&mode=gardeninit&farm=1&position=1',
                'productToCollect' => 1, //wheat 2x1
                'collectUrl' => 'http://s1.wolnifarmerzy.pl/ajax/farm.php?rid=yghjurtdvbhytrfvbnrec&mode=garden_harvest&farm=1&position=1&pflanze[]=1&feld[]=1&felder[]=1,2',
                'productToSeed' => 1, // wheat 2x1
                'seedUrl' => 'http://s1.wolnifarmerzy.pl/ajax/farm.php?rid=yghjurtdvbhytrfvbnrec&mode=garden_plant&farm=1&position=1&pflanze[]=1&feld[]=1&felder[]=1,2',
            ],
            [
                'spacePosition' => 3,
                'dashboardUrl' => 'http://s1.wolnifarmerzy.pl/ajax/farm.php?rid=yghjurtdvbhytrfvbnrec&mode=getfarms&farm=1&position=0',
                'gardenInitUrl' => 'http://s1.wolnifarmerzy.pl/ajax/farm.php?rid=yghjurtdvbhytrfvbnrec&mode=gardeninit&farm=1&position=3',
                'productToCollect' => 2, //Corn 2x2
                'collectUrl' => 'http://s1.wolnifarmerzy.pl/ajax/farm.php?rid=yghjurtdvbhytrfvbnrec&mode=garden_harvest&farm=1&position=3&pflanze[]=2&feld[]=1&felder[]=1,13,2,14',
                'productToSeed' => 2, //Corn 2x2
                'seedUrl' => 'http://s1.wolnifarmerzy.pl/ajax/farm.php?rid=yghjurtdvbhytrfvbnrec&mode=garden_plant&farm=1&position=3&pflanze[]=2&feld[]=1&felder[]=1,13,2,14',
            ],
            [
                'spacePosition' => 5,
                'dashboardUrl' => 'http://s1.wolnifarmerzy.pl/ajax/farm.php?rid=yghjurtdvbhytrfvbnrec&mode=getfarms&farm=1&position=0',
                'gardenInitUrl' => 'http://s1.wolnifarmerzy.pl/ajax/farm.php?rid=yghjurtdvbhytrfvbnrec&mode=gardeninit&farm=1&position=5',
                'productToCollect' => 17, //Carrot 1x1
                'collectUrl' => 'http://s1.wolnifarmerzy.pl/ajax/farm.php?rid=yghjurtdvbhytrfvbnrec&mode=garden_harvest&farm=1&position=5&pflanze[]=17&feld[]=1&felder[]=1',
                'productToSeed' => 1, //Wheat 2x1
                'seedUrl' => 'http://s1.wolnifarmerzy.pl/ajax/farm.php?rid=yghjurtdvbhytrfvbnrec&mode=garden_plant&farm=1&position=5&pflanze[]=1&feld[]=1&felder[]=1,2',
            ]
        ];
    }
}
