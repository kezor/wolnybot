<?php

namespace App\Connector;

use App\Field;
use App\Product\AbstractProduct;
use App\Player;
use App\Space;
use GuzzleHttp\Client;

class WolniFarmerzyConnector
{
    private $client;

    private $token;

    private $player;

    public function __construct()
    {
        $this->client = new Client(['cookies' => true]);
    }

    public function login(Player $player)
    {
        $this->player = $player;

        $res = $this->client->request('POST', 'https://www.wolnifarmerzy.pl/ajax/createtoken2.php?n=' . time(), [
            'form_params' => [
                'server' => $player->server_id,
                'username' => $player->username,
                'password' => $player->password,
                'ref' => '',
                'retid' => '',
                '_' => '',
            ],
        ]);

        $responseBody = $res->getBody()->__toString();

        $url = substr($responseBody, 4, strlen($responseBody) - 6);

        $url = str_replace('\\', '', $url);

        $res = $this->client->request('GET', $url);

        $body = $res->getBody()->__toString();

        $needle = 'var rid = \'';
        $startPos = strpos($body, $needle) + strlen($needle);

        $body = substr($body, $startPos);

        $length = strpos($body, '\'');

        $this->token = substr($body, 0, $length);
    }

    public function getDashboardData()
    {
        $allDataUrl = 'http://s' . $this->player->server_id . '.wolnifarmerzy.pl/ajax/farm.php?rid=' . $this->token . '&mode=getfarms&farm=1&position=0';
        $res = $this->client->request('GET', $allDataUrl);
        return json_decode($res->getBody()->__toString(), true);
    }

    public function getSpaceFields(Space $space)
    {
        $allDataUrl = 'http://s' . $this->player->server_id . '.wolnifarmerzy.pl/ajax/farm.php?rid=' . $this->token . '&mode=gardeninit&farm=1&position=' . $space->position;
        $res = $this->client->request('GET', $allDataUrl);
        return json_decode($res->getBody()->__toString(), true);
    }

    public function collect(AbstractProduct $product)
    {
        $url = 'http://s' . $this->player->server_id . '.wolnifarmerzy.pl/ajax/farm.php?rid=' . $this->token . '&mode=garden_harvest&farm=1&position=1&pflanze[]=' . $product->getPid() . '&feld[]=' . $product->getIndex() . '&felder[]=' . $product->getFields();
        $res = $this->client->request('GET', $url);
        return json_decode($res->getBody()->__toString(), true);
    }

    public function seed(Field $field)
    {
        $url = 'http://s' . $this->player->server_id . '.wolnifarmerzy.pl/ajax/farm.php?rid=' . $this->token . '&mode=garden_plant&farm=1&position=1&pflanze[]=' . $field->getProduct()->getPid() . '&feld[]=' . $field->index . '&felder[]=' . $field->getFields();
//        var_dump($url);
        return $this->client->request('GET', $url);
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

    public function getGuildingsOptions(Space $space)
    {
        $url = 'http://s' . $this->player->server_id . '.wolnifarmerzy.pl/ajax/farm.php?rid=' . $this->token . '&mode=getbuildingoptions&farm=' . $space->farm . '&position=' . $space->position;
        return $this->client->request('GET', $url);
    }

    public function buyBuilding(Space $space, $building)
    {
        $url = 'http://s' . $this->player->server_id . '.wolnifarmerzy.pl/ajax/farm.php?rid=' . $this->token . '&mode=buybuilding&farm=' . $space->farm . '&position=' . $space->position . '&id=1&buildingid=' . $building->getType();
        return $this->client->request('GET', $url);
    }

    public function watered(Field $field)
    {
        $url = 'http://s' . $this->player->server_id . '.wolnifarmerzy.pl/ajax/farm.php?rid=' . $this->token . '&mode=garden_water&farm=1&position=1&feld[]=' . $field->index . '&felder[]=' . $field->index;
        return $this->client->request('GET', $url);
    }

    //remove weed
    //http://s13.wolnifarmerzy.pl/ajax/farm.php?rid=03785d313df41243d7811e9385b0f288&mode=garden_removeweed&farm=1&position=1&id=38&tile=38

    // sell customer
    //http://s15.wolnifarmerzy.pl/ajax/farm.php?rid=f7f4f87f2bdf63ad0e04109334963e3d&mode=sellfarmi&farm=1&position=1&id=52621097&farmi=52621097&status=1
}