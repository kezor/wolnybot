<?php
/**
 * Created by PhpStorm.
 * User: maciej
 * Date: 23.05.17
 * Time: 16:25
 */

namespace App\Connector;


use App\Field;
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

    public function collect(Field $field)
    {
        $allDataUrl = 'http://s' . $this->player->server_id . '.wolnifarmerzy.pl/ajax/farm.php?rid=' . $this->token . '&mode=garden_harvest&farm=1&position=1&pflanze[]='.$field->plant_type.'&feld[]='.$field->offset_x.'&felder[]='.$field->offset_x;
        $res = $this->client->request('GET', $allDataUrl);
        return json_decode($res->getBody()->__toString(), true);
    }

    public function seed(Field $field, $plantType)
    {
        echo 'Try to seed on field'.$field->id.PHP_EOL;
        $url = 'http://s' . $this->player->server_id . '.wolnifarmerzy.pl/ajax/farm.php?rid=' . $this->token . '&mode=garden_plant&farm=1&position=1&pflanze[]='.$plantType.'&feld[]=' . $field->offset_x . '&felder[]=' . $field->offset_x . '&cid=15';
        return $this->client->request('GET', $url);
    }
}