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

require_once __DIR__ . '/vendor/autoload.php';

use Curl\Curl;

class SwitcheoApiClient {

    private $curl;
    private $url = 'https://tradescan.switcheo.org/';
    //private $url = 'https://legacy-scan.carbon.network/';
    public $api_limit =200;
    public $only_success = true;//not working

    public function __construct()
    {
        $this->curl = new Curl();
        $this->curl->setOpt(CURLOPT_CONNECTTIMEOUT, 0);
        $this->curl->setOpt(CURLOPT_TIMEOUT, 60);
    }

    public function getUnbonds($after_id = 0)
    {
        return $this->getTransactions([
            'msg_type'=>'begin_unbonding',
            'after_id'=>$after_id,
            'limit'=>$this->api_limit,
            'order_by'=>'asc'
        ]);
    }

    public function getRewards($after_id = 0)
    {
        return $this->getTransactions([
            'msg_type'=>'withdraw_delegator_reward',
            'after_id'=>$after_id,
            'limit'=>$this->api_limit,
            'order_by'=>'asc'
        ]);
    }

    public function getWithdrawals($after_id = 0)
    {
        return $this->getTransactions([
            'msg_type'=>'withdraw',
            'after_id'=>$after_id,
            'limit'=>$this->api_limit,
            'order_by'=>'asc'
        ]);
    }

    public function getDelegates($after_id = 0)
    {
        return $this->getTransactions([
            'msg_type'=>'delegate,create_validator',
            'after_id'=>$after_id,
            'limit'=>$this->api_limit,
            'order_by'=>'asc'
        ]);
    }

    public function getWithRewards($after_id = 0)
    {
        return $this->getTransactions([
            'msg_type'=>'withdraw_delegator_reward,begin_unbonding,begin_redelegate,delegate,claim_pool_rewards,stake_pool_token',
            'after_id'=>$after_id,
            'limit'=>$this->api_limit,
            'order_by'=>'asc'
        ]);
    }

    public function getUnjails($after_id = 0)
    {
        return $this->getTransactions([
            'msg_type'=>'unjail',
            'after_id'=>$after_id,
            'limit'=>$this->api_limit,
            'order_by'=>'asc'
        ]);
    }

    public function getSends($after_id = 0)
    {
        return $this->getTransactions([
            'msg_type'=>'send',
            'after_id'=>$after_id,
            'limit'=>$this->api_limit,
            'order_by'=>'asc'
        ]);
    }

    public function getTransactionLogs($hash)
    {
        return $this->getTxLogs([
            'hash'=>$hash
        ]);
    }

    public function getExternalTransfers($wallet)
    {
        return $this->getExtTransfers([
            'account'=>$wallet
        ]);
    }

    public function getTokens(){
        return $this->simpleGet('get_tokens',[]);
    }

    public function getMarkets(){
        return $this->simpleGet('get_markets',[]);
    }

    public function getTrading($params){
        return $this->simpleGet('get_trades',$params);
    }

    public function getTransactionTypes(){
        return $this->simpleGet('get_transaction_types',[]);
    }


    private function getTxLogs($params)
    {
        $attempts=5;
        retry:
        $this->curl->get($this->url . 'get_tx_log',$params);
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

    function getTransaction($hash)
    {
        $attempts=5;
        retry:
        $this->curl->get($this->url . 'get_transaction',['hash'=>$hash]);
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

    function getTransactions($params)
    {
        $attempts=5;
        retry:
        //var_dump('URL:',$this->url . 'get_transactions' . '?' . http_build_query($params));
        $this->curl->get($this->url . 'get_transactions',$params);
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

    public function getValidators()
    {
        $attempts=5;
        retry:
        $this->curl->get($this->url . 'get_all_validators');
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

    private function getExtTransfers($params)
    {
        $attempts=5;
        retry:
        $this->curl->get($this->url . 'get_external_transfers',$params);
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