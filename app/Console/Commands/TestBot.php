<?php

namespace App\Console\Commands;

use GuzzleHttp\Client;
use Illuminate\Console\Command;

class TestBot extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bot:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    private $client;

    private $token;

    public function __construct()
    {
        parent::__construct();
        $this->client = new Client(['cookies' => true]);
        $this->getLoginAndGetToken();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->collect(5);
        $this->collect(6);
        $this->collect(7);
    }

    private function getLoginAndGetToken()
    {
        $res = $this->client->request('POST', 'https://www.wolnifarmerzy.pl/ajax/createtoken2.php?n=' . time(), [
            'form_params' => [
                'server' => 15,
                'username' => 'KapitanZordon',
                'password' => 'dupadupa',
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

    private function seedCarrot($position)
    {
        $url = 'http://s15.wolnifarmerzy.pl/ajax/farm.php?rid=' . $this->token . '&mode=garden_plant&farm=1&position=1&pflanze[]=17&feld[]=' . $position . '&felder[]=' . $position . '&cid=15';
        return $this->client->request('GET', $url);
    }

    private function collect($position)
    {
        $url = 'http://s15.wolnifarmerzy.pl/ajax/farm.php?rid='.$this->token.'&mode=garden_harvest&farm=1&position=1&pflanze[]=17&feld[]='.$position.'&felder[]='.$position;
        return $this->client->request('GET', $url);
    }

    private function getAllData()
    {
        $allDataUrl = 'http://s15.wolnifarmerzy.pl/ajax/farm.php?rid=' . $this->token . '&mode=getfarms&farm=1&position=0';
        return $this->client->request('GET', $allDataUrl);
    }
}
