<?php
/**
 * Created by PhpStorm.
 * User: maciej
 * Date: 23.05.17
 * Time: 16:25
 */

namespace App\Connector;


use App\Player;
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
        $allDataUrl = 'http://s'.$this->player->server_id.'.wolnifarmerzy.pl/ajax/farm.php?rid=' . $this->token . '&mode=getfarms&farm=1&position=0';
        $res = $this->client->request('GET', $allDataUrl);
        return json_decode($res->getBody()->__toString(), true);
    }
}