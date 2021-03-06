<?php

namespace App\Connector;

use Config;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

abstract class BaseConnector
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @param        $url
     * @param string $method
     * @return bool|mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function callRequest($url, $method = 'GET')
    {
        try {
            // small delay before each call
            sleep(mt_rand(3, 7) / 10);
            $res = $this->client->request($method, $url);

            $resJson = json_decode($res->getBody()->__toString(), true);

            if (Config::get('app.logAllRequests')) {
                Log::debug($url . ':' . $method, $resJson);
            }

            return $resJson;
        } catch (\Exception $exception) {
            return false;
        }
    }
}