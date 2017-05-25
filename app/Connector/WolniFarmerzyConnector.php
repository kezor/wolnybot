<?php
/**
 * Created by PhpStorm.
 * User: maciej
 * Date: 23.05.17
 * Time: 16:25
 */

namespace App\Connector;


use App\Field;
use App\Plant\AbstractPlant;
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

    public function collect(AbstractPlant $plant)
    {
        $url = 'http://s' . $this->player->server_id . '.wolnifarmerzy.pl/ajax/farm.php?rid=' . $this->token . '&mode=garden_harvest&farm=1&position=1&pflanze[]=' . $plant->getType() . '&feld[]=' . $plant->getIndex() . '&felder[]=' . $plant->getFields();
        $res = $this->client->request('GET', $url);
        return json_decode($res->getBody()->__toString(), true);
    }

    public function seed(Field $field, $plantType)
    {
        $url = 'http://s' . $this->player->server_id . '.wolnifarmerzy.pl/ajax/farm.php?rid=' . $this->token . '&mode=garden_plant&farm=1&position=1&pflanze[]=' . $plantType . '&feld[]=' . $field->index . '&felder[]=' . $field->index;
        return $this->client->request('GET', $url);
    }

    public function disableTutorial()
    {
        $url = 'http://s' . $this->player->server_id . '.wolnifarmerzy.pl/ajax/main.php?rid=' . $this->token . '&action=closetutorial';
        return $this->client->request('GET', $url);
    }

    public function increaseTutorialStep()
    {
        $url = 'http://s' . $this->player->server_id . '.wolnifarmerzy.pl/ajax/main.php?rid=' . $this->token . '&action=increasetutorialstep';
        return $this->client->request('GET', $url);
    }

    //http://s8.wolnifarmerzy.pl/ajax/farm.php?rid=fe3faac43740b3f28e6d6bba45c633cb&mode=getbuildingoptions&farm=1&position=1
    //http://s8.wolnifarmerzy.pl/ajax/farm.php?rid=fe3faac43740b3f28e6d6bba45c633cb&mode=buybuilding&farm=1&position=1&id=1&buildingid=1
    //http://s8.wolnifarmerzy.pl/ajax/farm.php?rid=fe3faac43740b3f28e6d6bba45c633cb&mode=gardeninit&farm=1&position=1
    //http://s8.wolnifarmerzy.pl/ajax/farm.php?rid=fe3faac43740b3f28e6d6bba45c633cb&mode=garden_plant&farm=1&position=1&pflanze[]=17&feld[]=3&felder[]=3&cid=12
    //http://s8.wolnifarmerzy.pl/ajax/farm.php?rid=fe3faac43740b3f28e6d6bba45c633cb&mode=garden_water&farm=1&position=1&feld[]=3&felder[]=3
    //wait 15-20 sec
    //http://s8.wolnifarmerzy.pl/ajax/farm.php?rid=fe3faac43740b3f28e6d6bba45c633cb&mode=garden_harvest&farm=1&position=1&pflanze[]=17&feld[]=3&felder[]=3
    //increate
    //closetutorial
}