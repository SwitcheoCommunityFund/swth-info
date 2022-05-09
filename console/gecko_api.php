<?php


require_once __DIR__ . '/vendor/autoload.php';

use Curl\Curl;

class GeckoApiClient {

    private $curl;
    private $url = 'https://api.coingecko.com/api/v3/';
    public $api_limit =200;

    public function __construct()
    {
        $this->curl = new Curl();
    }

    public function getTokenHistory($token,$date)
    {
        $method = "coins/{$token}/history";
        $params = [
            'date'=>$date,
            'localization'=>false
        ];
        return $this->simpleGet($method,$params);
    }


    public function getLastTokenPrice($token)
    {
        //https://api.coingecko.com/api/v3/coins/switcheo?localization=false&&&&
        $method = "coins/{$token}";
        $params = [
            'localization'=>false,
            'tickers'=>false,
            'community_data'=>false,
            'developer_data'=>false,
            'sparkline'=>false,
        ];
        return $this->simpleGet($method,$params);
    }

    private function simpleGet($method,$params)
    {
        $attempts=5;
        $retrysec=10;
        retry:
        $this->curl->get($this->url . $method,$params);
        //if($this->only_success) $params['code']='0';//not working
        if ($this->curl->error) {
            echo 'Error: ' . $this->curl->errorCode . ': ' . $this->curl->errorMessage . "\n";
            if(--$attempts>=0){
                sleep($retrysec);
                echo "Retry request after {$retrysec}sec, attempts left $attempts\n";
                goto retry;
            }
        } else {
            return $this->curl->response;
        }
        return false;
    }

}