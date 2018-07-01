<?php

namespace App\Connector;

use App\Building\Farmland;
use App\Building\Hovel;
use App\Field;
use App\Player;
use App\Product;
use App\UrlGenerator;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use App\Space;

class WolniFarmerzyConnector implements ConnectorInterface
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var string
     */
    private $token;

    /**
     * @var Player
     */
    private $player;

    /**
     * @var UrlGenerator
     */
    public $urlGenerator;

    public function __construct($client = null)
    {
        if (!$client) {
            $client = new Client(['cookies' => true]);
        }
        $this->client = $client;
    }

    private $loggedIn = false;

    public function login(Player $player)
    {
        $this->player = $player;

        try {
            $res = $this->client->request('POST', 'https://www.wolnifarmerzy.pl/ajax/createtoken2.php?n=' . time(), [
                'form_params' => [
                    'server'   => $player->server_id,
                    'username' => $player->username,
                    'password' => $player->password,
                    'ref'      => '',
                    'retid'    => '',
                    '_'        => '',
                ],
            ]);

            $responseBody = $res->getBody()->__toString();

            $matches = null;
            preg_match('^\[1,"[a-z\:]+^', $responseBody, $matches);
            if (empty($matches)) {
                throw new \Exception('Wrong login credentials');
            }

            $url = substr($responseBody, 4, strlen($responseBody) - 6);

            $url = str_replace('\\', '', $url);

            $res = $this->client->request('GET', $url);

            $body = $res->getBody()->__toString();

            $needle   = 'var rid = \'';
            $startPos = strpos($body, $needle) + strlen($needle);

            $body = substr($body, $startPos);

            $length = strpos($body, '\'');

            $this->token = substr($body, 0, $length);

            if (empty($this->token)) {
                throw new \Exception('Token is invalid');
            }

            $this->urlGenerator = new UrlGenerator($player, $this->token);
        } catch (\Exception $exception) {
            Log::error('Error during login process -> ' . json_encode(['message' => $exception->getMessage(), 'strace' => $exception->getTraceAsString()]));

            $this->loggedIn = false;

            return false;
        }
        $this->loggedIn = true;

        return true;
    }

    public function isLoggedIn()
    {
        return $this->loggedIn;
    }

    public function getDashboardData()
    {
        $url          = $this->urlGenerator->getGetFarmUrl();
        $responseData = $this->callRequest($url);
        if (!$responseData) {
            Log::alert('Failed to get dashboard data: url - ' . $url);
        }

        return $responseData;
    }

    public function getFarmlandFields(Farmland $farmland)
    {
        $allDataUrl = $this->urlGenerator->getGardenInitUrl($farmland);
        $res        = $this->client->request('GET', $allDataUrl);

        return json_decode($res->getBody()->__toString(), true);
    }

    public function collect(Farmland $farmland, Field $field)
    {
        $url          = $this->urlGenerator->getGardenHarvestUrl($farmland, $field);
        $responseData = $this->callRequest($url);
        if (!$responseData) {
            Log::alert('Failed to collect field: farmland - ' . serialize($farmland) . ', field - ' . serialize($field) . $url);
        }

        return $responseData;
    }

    public function seed(Farmland $farmland, Field $field)
    {
        $url          = $this->urlGenerator->getGardenPlantUrl($farmland, $field);
        $responseData = $this->callRequest($url);
        if (!$responseData) {
            Log::alert('Failed to seed field: farmland - ' . serialize($farmland) . ', field - ' . serialize($field) . $url);
        }

        return $responseData;
    }

    public function closeTutorial()
    {
        $url = 'http://s' . $this->player->server_id . '.wolnifarmerzy.pl/ajax/main.php?rid=' . $this->token . '&action=closetutorial';

        return $this->client->request('GET', $url);
    }

    public function increaseTutorialStep()
    {
        $url = 'http://s' . $this->player->server_id . '.wolnifarmerzy.pl/ajax/main.php?rid=' . $this->token . '&action=increasetutorialstep';

        return $this->client->request('GET', $url);
    }

    public function getBuildingsOptions(Space $space)
    {
        $url = 'http://s' . $this->player->server_id . '.wolnifarmerzy.pl/ajax/farm.php?rid=' . $this->token . '&mode=getbuildingoptions&farm=' . $space->farm . '&position=' . $space->position;

        return $this->client->request('GET', $url);
    }

    public function buyBuilding(Space $space, $building)
    {
        $url = 'http://s' . $this->player->server_id . '.wolnifarmerzy.pl/ajax/farm.php?rid=' . $this->token . '&mode=buybuilding&farm=' . $space->farm . '&position=' . $space->position . '&id=1&buildingid=' . $building->getType();

        return $this->client->request('GET', $url);
    }

    public function waterField(Farmland $farmland, Field $field)
    {
        $url          = $this->urlGenerator->getGardenWaterUrl($farmland, $field);
        $responseData = $this->callRequest($url);
        if (!$responseData) {
            Log::alert('Failed to water field: farmland - ' . serialize($farmland) . ', field - ' . serialize($field) . $url);
        }

        return $responseData;
    }

    private function callRequest($url)
    {
        try {
            // small delay before each call
            sleep(mt_rand(3, 7) / 10);
            $res = $this->client->request('GET', $url);

            return json_decode($res->getBody()->__toString(), true);
        } catch (\Exception $exception) {
            return false;
        }
    }

    public function initHovel(Hovel $hovel)
    {
        $url          = $this->urlGenerator->getLoadHovelDataUrl($hovel);
        $responseData = $this->callRequest($url);
        if (!$responseData) {
            Log::alert('Failed to get hovel data: url - ' . $url);
        }

        return $responseData;
    }

    public function collectEggs(Hovel $hovel)
    {
        $url          = $this->urlGenerator->getCollectEggsUrl($hovel);
        $responseData = $this->callRequest($url);
        if (!$responseData) {
            Log::alert('Failed to collect eggs from hovel data: url - ' . $url);
        }

        return $responseData;    }

    public function feedChickens(Hovel $hovel, Product $product)
    {
        $url          = $this->urlGenerator->getFeedChickensUrl($hovel, $product);
        $responseData = $this->callRequest($url);
        if (!$responseData) {
            Log::alert('Failed to feed chickens data: url - ' . $url);
        }

        return $responseData;
    }

    //remove weed
    //http://s13.wolnifarmerzy.pl/ajax/farm.php?rid=03785d313df41243d7811e9385b0f288&mode=garden_removeweed&farm=1&position=1&id=38&tile=38

    // sell customer
    //http://s15.wolnifarmerzy.pl/ajax/farm.php?rid=f7f4f87f2bdf63ad0e04109334963e3d&mode=sellfarmi&farm=1&position=1&id=52621097&farmi=52621097&status=1
}