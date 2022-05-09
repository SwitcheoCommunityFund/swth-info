<?php


namespace app\helpers;

use Yii;
use Curl\Curl;
use app\models\FaucetLocker;
use yii\db\Expression;
use kornrunner\Secp256k1;
use kornrunner\Serializer\HexSignatureSerializer;
use Suin\Marshaller\JsonMarshaller;
use Suin\Marshaller\StandardProtocol;

//use Minter\MinterAPI;
use Minter\SDK\MinterTx;
use Minter\SDK\MinterCoins\MinterSendCoinTx;

class FaucetHelper
{
    private $curl, $wallet, $walletLength=43, $walletPref='swth1';
    private $limitHours=24;
    private $limitPerMonth=3;
    private $balanceMaxLimit=2;
    private $maxSendLimit=2;
    private $endpointUrl='https://tradescan.switcheo.org';
    private $localEndpointUrl='http://localhost:8080';
    private $faucet_locker, $api;

    private $fundWallet = '****';
    private $privateKey = '****';

    private $limitations = [

    ];

    public $failStack=[];

    public function __construct($wallet,$amount)
    {
        $session = Yii::$app->session;
        if(!$session->isActive){ $session->open(); }
        $this->wallet = mb_strtolower($wallet);
        $this->amount = min(max((int)$amount,1),$this->maxSendLimit);
        $this->curl = new Curl();
        $this->initMessages();
    }

    private function initMessages()
    {
        $this->limitations = [
            'daily_limit'     => "Unable to send, once per {$this->limitHours} hours allowed",
            'month_limit'     => "Unable to send, only 3 transfers per wallet allowed",
            'not_found'       => "Incorrect wallet address",
            'bad_wallet'      => "Incorrect wallet address",
            'high_balance'    => "Available balance should be less than {$this->balanceMaxLimit} SWTH",
            'try_later'       => "Please try in 5 minutes",
            'try_in'          => "Please try in",
            'node_try_later'  => "Inactive node, please try again later",
            'something_wrong' => "Something went wrong, please try again later",
            'node_problem'    => "Problem with node, please try again later",
        ];
    }

    /* ============================= CHECKERS ============================== */

    public function checkIp()
    {

    }

    public function checkCookie()
    {
        $session = Yii::$app->session;
        $time = time();
        if(($left = (int)$session->get('faucetlockuntil')-$time)>0){
            return $this->fail('daily_limit','('.$this->prettyInterval($left).' left)');
        } return true;
    }

    public function checkBalance()
    {
        $res = $this->request('get_balance',['account'=>$this->wallet]);
        if($res['status']=='success'){
            if(!$res['data']){
                $this->fail('not_found');
                return false;
            }
            //return $res['data'];
            $balances = array_column((array)$res['data'],'available','denom');

            if(@$balances['swth'] >= $this->balanceMaxLimit){
                return $this->fail('high_balance');
            } else return true;
        } else {
            $this->fail($res['message']);
            return false;
        }
    }

    public function checkWalletName()
    {
        $pref = preg_quote($this->walletPref);
        $length = $this->walletLength - strlen($this->walletPref);
        if(!preg_match("/^{$pref}[a-z0-9]{{$length},{$length}}$/",$this->wallet)){
            return $this->fail('bad_wallet');
        } else {
            $res = $this->request('get_account',['account'=>$this->wallet]);
            if(@$res['data']->error || empty($res['data']))
                return $this->fail('bad_wallet');
            else
                return true;
        }
    }

    public function checkWalletExists()
    {

    }

    public function checkLocker()
    {
        //$expression = new Expression('greatest(EXTRACT(EPOCH FROM (fl.lock_until-current_timestamp))/60,0)');
        $lock_time_expr = new Expression('current_timestamp + interval \'5 minutes\' ');
        $curr_db_time  = Yii::$app->db->createCommand('SELECT current_timestamp')->queryScalar();
        $curr_db_month = Yii::$app->db->createCommand("SELECT date_trunc('month',current_date)")->queryScalar();

        //return [$curr_db_time,substr($curr_db_month,0,10)];

        if($this->faucet_locker = FaucetLocker::findOne($this->wallet))
        {

            $this->faucet_locker->count_tries += 1;

            if(substr($curr_db_month,0,10) == $this->faucet_locker->last_month)
            {
                if($this->faucet_locker->count_month >= $this->limitPerMonth){
                    $this->faucet_locker->save();
                    return $this->fail('month_limit');
                }
            } else {
                $this->faucet_locker->count_month = 0;
                $this->faucet_locker->last_month = substr($curr_db_month,0,10);
            }

            $left = strtotime($this->faucet_locker->lock_until) - strtotime($curr_db_time);// <= 0;
            if($left<=0){
                $this->faucet_locker->lock_until=$lock_time_expr;
            }
            $this->faucet_locker->save();
            return max($left,0)>0?$this->fail('try_in',$this->prettyInterval($left)):true;
        } else {
            $this->faucet_locker = new FaucetLocker();
            $this->faucet_locker->wallet = $this->wallet;
            $this->faucet_locker->count_tries = 1;
            $this->faucet_locker->save();
            return true;
        }
    }

    public function checkLockerOld()
    {
        //$expression = new Expression('greatest(EXTRACT(EPOCH FROM (fl.lock_until-current_timestamp))/60,0)');
        $lock_time_expr = new Expression('current_timestamp + interval \'5 minutes\' ');
        $curr_db_time = Yii::$app->db->createCommand('SELECT current_timestamp')->queryScalar();
        //$curr_db_month = Yii::$app->db->createCommand("SELECT date_trunc('month',current_date)")->queryScalar();

        if($this->faucet_locker = FaucetLocker::findOne($this->wallet))
        {
            $this->faucet_locker->count_tries += 1;

            $left = strtotime($this->faucet_locker->lock_until) - strtotime($curr_db_time);// <= 0;
            if($left<=0){
                $this->faucet_locker->lock_until=$lock_time_expr;
            }
            $this->faucet_locker->save();
            return max($left,0)>0?$this->fail('try_in',$this->prettyInterval($left)):true;
        } else {
            $this->faucet_locker = new FaucetLocker();
            $this->faucet_locker->wallet = $this->wallet;
            $this->faucet_locker->count_tries = 1;
            $this->faucet_locker->save();
            return true;
        }
    }

    public function checkLastTransfer()
    {
        $timeLimit = strtotime('-24 hours');
        $minListTime = time();

        $params = [
            'address'=>$this->fundWallet,
            'msg_type'=>'send',
            'order_by'=>'desc',
        ];

        while(1){
            $res = $this->request('get_transactions',$params);
            //return '/get_transactions?'.http_build_query($params);
            if($res['status']=='error') return $this->fail($res['message']);
            if(empty($res['data'])) break;
            $trs = $res['data'];
            foreach ($trs as $tr){
                $tr_msg = json_decode(@$tr->msg);
                if(!$tr_msg) return $this->fail(@$tr);
                if($tr->code!=0){continue;}
                if($tr_msg->to_address == $this->wallet && $timeLimit < strtotime($tr->block_time)){
                    return $this->fail('daily_limit');
                }

                $minListTime = min(strtotime($tr->block_time),$minListTime);
                $params['before_id'] = (@$params['before_id'])?min(@$params['before_id'],$tr->id):$tr->id;
            }
            if($timeLimit > $minListTime) return true;
        }
        return true;
    }

    public function nodeState()
    {
        exec("ps -aux | grep 'faucet.js' | grep -v grep | wc -l", $output, $retval);
        return ((int)(@$output[0]))>0?true:$this->fail('node_try_later');
    }








    /* ============================= SETTERS ============================== */

    public function unlockLocker()
    {
        $lock_time_expr = new Expression('current_timestamp');
        $this->faucet_locker = FaucetLocker::findOne($this->wallet);
        $this->faucet_locker->lock_until = $lock_time_expr;
        $this->faucet_locker->save();
    }

    private function setCookieLock()
    {
        $session = Yii::$app->session;
        $time = time();
        $session->set('faucetlockuntil', $time+3600*$this->limitHours);
    }

    private function setLockerLock()
    {
        $lock_time_expr = new Expression("current_timestamp + interval '24 hours'");
        $curr_db_month = Yii::$app->db->createCommand("SELECT date_trunc('month',current_date)")->queryScalar();
        $this->faucet_locker = FaucetLocker::findOne($this->wallet);
        $this->faucet_locker->lock_until = $lock_time_expr;
        $this->faucet_locker->count_success += 1;
        $this->faucet_locker->last_month = substr($curr_db_month,0,10);
        $this->faucet_locker->count_month += 1;
        $this->faucet_locker->save();
    }

    public function sendTokensMint()
    {
        //$api = new MinterAPI($this->endpointUrl.'/txs');
        $data = new MinterSendCoinTx(6, $this->wallet, '1');
        return $data;
        $tx = new MinterTx($nonce, $data);

        return $signed_tx = $tx->sign($this->privateKey);
        $response = $api->send($signed_tx);
    }

    public function sendTokensJs()
    {
        $res = $this->simpleRequest('',['wallet'=>$this->wallet,'amount'=>$this->amount],'POST');
        if($res['status']=='success')
        {
            if(@$res['data']->status=='success')
            {
                if(@$res['data']->api_request->txhash)
                {
                    $tx_res = $this->request('get_transaction',['hash'=>$res['data']->api_request->txhash]);
                    if(@$tx_res['status']=='success' && @$tx_res['data']->code==0) return true; else return $this->fail('something_wrong');
                } else return $this->fail('something_wrong');
            } else return $this->fail('node_problem');
        } else return $this->fail('node_problem');
    }


    public function successSendTokens()
    {
        try {
            $this->setCookieLock();
            $this->setLockerLock();
        }catch (\Exception $e){}
    }

    public function sendTokens()
    {
        //$secp256k1 = new Secp256k1();
        $context = \secp256k1_context_create(SECP256K1_CONTEXT_SIGN | SECP256K1_CONTEXT_VERIFY);
        //$marshaller = new JsonMarshaller(new StandardProtocol);

        $raw_message = [
            'type'=>'cosmos-sdk/MsgSend',
            'value'=>[
                'amount'=>[['amount'=> (string)(100000000 * $this->amount),'denom'=>'swth']],
                'from_address' => $this->fundWallet,
                'to_address' => $this->wallet,
            ]
        ];

        $signing_message = [
            'accountNumber'=>'5',
            'chainId'=>'3',
            'fee'=>[ 'amount'=>[['denom'=>'swth', 'amount'=>'100000000']],'gas'=>'100000000000'],
            'msgs'=>[$raw_message],
            'sequence' => '1',
        ];


        $post = [
            'mode'=> 'block',
            'tx'=>[
                'msg' => [
                    $raw_message
                ],
            ]
        ];

        //$marshal_message = $marshaller->marshal((object) $signing_message);

        $msg32 = hash('sha256', json_encode($signing_message), true);
        //$msg32 = hash('sha256', $marshal_message, true);
        //$privateKey = pack("H*", $this->privateKey);
        $privateKey = pack("H*", hash('sha256',$this->privateKey));


        $signature = null;
        if (1 !== \secp256k1_ecdsa_sign($context, $signature, $msg32, $privateKey)) {
            throw new \Exception("Failed to create signature");
        }

        $serialized = '';
        \secp256k1_ecdsa_signature_serialize_der($context, $serialized, $signature);
        $hexSignature = bin2hex($serialized);

        //$pubKey = openssl_pkey_get_public($this->privateKey);
        //$signature = $secp256k1->sign(hash('sha256',json_encode($post),true), $this->privateKey);


        $publicKey = null;
        $result = \secp256k1_ec_pubkey_create($context, $publicKey, $privateKey);
        if ($result === 1) {
            $serializeFlags = SECP256K1_EC_COMPRESSED;

            $serialized = '';
            if (1 !== secp256k1_ec_pubkey_serialize($context, $serialized, $publicKey, $serializeFlags)) {
                throw new \Exception('secp256k1_ec_pubkey_serialize: failed to serialize public key');
            }

            $hexPublicKey = unpack("H*", $serialized)[1];
        } else {
            throw new \Exception('secp256k1_pubkey_create: secret key was invalid');
        }


        //$serializer = new HexSignatureSerializer();
        //$signatureString = $serializer->serialize($signature);
        //$signatureString = $signature->toHex();

        $signatures = [
            'pub_key' => [
                'type'=> 'tendermint/PubKeySecp256k1',
                'value'=> $hexPublicKey,
            ],
            'signature'=> $hexSignature
        ];
        $post['tx']['signatures']=$signatures;

        //$marshal_post = $marshaller->marshal($post);

        return $this->txRequest($post);
    }

    /*====================== Other ======================== */

    private function prettyInterval($secInt)
    {
        $h = floor($secInt/3600);
        $m = floor(($secInt%3600)/60);
        $s = ($secInt%3600)%60;

        return
            ($h>0?"$h hour":null).' '.
            ($m>0?"$m min":null).' '.
            ($s>0?"$s sec":null);
    }


    private function request($path, $params, $method='GET')
    {
        $attempts=5;
        retry:
        $this->curl->{mb_strtolower($method)}($this->endpointUrl.'/'.$path, $params);
        //if($this->only_success) $params['code']='0';//not working
        if ($this->curl->error) {
            //echo 'Error: ' . $this->curl->errorCode . ': ' . $this->curl->errorMessage . "\n";
            if(--$attempts>=0){
                sleep(3);
                //echo "Retry request after 5sec, attempts left $attempts\n";
                goto retry;
            } else {
                return [
                    'status'  => 'error',
                    'code'    => $this->curl->errorCode,
                    'message' => $this->curl->errorMessage,
                    'data'   => @$this->curl->response,
                ];
            }
        } else {
            return [
                'status' => 'success',
                'data'   => $this->curl->response,
            ];
        }

    }

    private function simpleRequest($path, $params, $method='GET')
    {
        $attempts=5;
        retry:
        $this->curl->{mb_strtolower($method)}($this->localEndpointUrl . $path, $params);
        //if($this->only_success) $params['code']='0';//not working
        if ($this->curl->error) {
            //echo 'Error: ' . $this->curl->errorCode . ': ' . $this->curl->errorMessage . "\n";
            if(--$attempts>=0){
                sleep(3);
                //echo "Retry request after 5sec, attempts left $attempts\n";
                goto retry;
            } else {
                return [
                    'status'  => 'error',
                    'code'    => $this->curl->errorCode,
                    'message' => $this->curl->errorMessage,
                    'data'   => @$this->curl->response,
                    'url'    => $this->localEndpointUrl . $path
                ];
            }
        } else {
            return [
                'status' => 'success',
                'data'   => $this->curl->response,
            ];
        }

    }

    private function txRequest($params)
    {
        //$params = json_encode($params);
        $this->curl->setHeader('content-type','application/json');
        $this->curl->post($this->endpointUrl.'/txs', $params);
        if ($this->curl->error) {
            return [
                'status'  => 'error',
                'code'    => $this->curl->errorCode,
                'message' => $this->curl->errorMessage,
                'data'    => @$this->curl->response,
                'params'  => $params,
            ];
        } else {
            return [
                'status' => 'success',
                'data'   => $this->curl->response,
            ];
        }
    }

    private function fail($name,$add=null){
        $this->failStack[] = (!@$this->limitations[$name]?$name:$this->limitations[$name]).' '.$add;
        return false;
    }

}