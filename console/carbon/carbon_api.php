<?php



/**
 * [API Filters /get_transactions] *
 * after_id = int
 * before_id = int
 * address = string64
 * msg_type = string ('submit_proposal', 'withdraw', 'send', 'sync_headers', 'link_token', 'delegate', 'begin_redelegate', 'update_profile', 'create_validator', 'process_cross_chain_tx', 'sync_genesis', 'edit_validator', 'vote', 'withdraw_validator_commission', 'begin_unbonding', 'activate_sub_account', 'unjail', 'withdraw_delegator_reward', 'create_sub_account', 'create_token', 'set_max_validator_17', 'enable_inflation', 'run_upgrade')
 * limit = int (max 200)
 * order_by = string (asc or desc)
 * */

/**
 * [API Filters /get_tx_log] *
 * hash = string64
 * */

require_once __DIR__ . '/../vendor/autoload.php';

use Curl\Curl;

class CarbonApiClient {

    private $curl;
    private $url = 'https://tm-api.carbon.network/';
    //private $url = 'https://legacy-scan.carbon.network/';
    public $api_limit =200;
    public $only_success = true;//not working

    public function __construct()
    {
        $this->curl = new Curl();
        $this->curl->setOpt(CURLOPT_CONNECTTIMEOUT, 0);
        $this->curl->setOpt(CURLOPT_TIMEOUT, 60);
    }

    public function TxSearch($block_height){
        return $this->simpleGet('tx_search',['query'=>"\"tx.height=$block_height\""]);
    }


    private function simpleGet($method,$params)
    {
        $attempts=5;
        retry:
        $this->curl->get($this->url . $method,$params);
        //if($this->only_success) $params['code']='0';//not working
        if ($this->curl->error) {
            echo 'Error: ' . $this->curl->errorCode . ': ' . $this->curl->errorMessage . "\n";
            if(--$attempts>=0){
                sleep(5);
                echo "Retry request after 5sec, attempts left $attempts\n";
                goto retry;
            }
        } else {
            return $this->curl->response;
        }
        return false;
    }

}