<?php


include_once __DIR__ . '/vendor/autoload.php';
include_once __DIR__ . '/swth_api.php';
include_once __DIR__ . '/connection.php';
include_once __DIR__ . '/common.php';

errorReaction();

$c = new Connection();
$db = $c->db;

$table_name = 'rewards';


$api = new SwitcheoApiClient();
$api->api_limit = 100;

$last_id = max(getLastId($table_name)-1,0);


$query = $c::makeNoUpsertQuery($table_name,/*['id','denom'],*/['id', 'wallet', 'validator', 'date', 'value', 'denom', 'tr_type', 'tr_hash']);
$stmt = $db->prepare($query);

$previous_tr_logs = [
    'hash'=>0,
    'logs'=>[]
];

$msg_index = 0;

$message_types = ['withdraw_delegator_reward','begin_unbonding','begin_redelegate','delegate','claim_pool_rewards','stake_pool_token'];

$enums = $c->getEnums();

do {
    $trs = $api->getWithRewards($last_id);
    if(!$trs) {
        echo("\n-Last id is: $last_id \n-Transactions not found after\n");
        if($trs===false) throw new \Exception('Transactions load error');
        return;
    }
    foreach ($trs as $tr)
    {
        if($tr->code!=0){echo 'x'; continue;} // FAIL REWARDS
        $tr_msg = json_decode($tr->msg);

        $validator = @$tr_msg->validator_address?$tr_msg->validator_address:@$tr_msg->validator_src_address;
        $data = [
            'id'        => $tr->id,
            'wallet'    => $tr->address,
            'validator' => $validator,
            'date'      => $tr->block_time,
            'tr_type'   => $tr->msg_type,
            'tr_hash'   => $tr->hash
        ];

        checkEnums($enums,$data,['tr_type'=>'transaction_type']);

        $dataHash = dataHash($data);
        if($previous_tr_logs['hash'] !== $tr->hash)
        {
            $logs_raw = $api->getTransactionLogs($tr->hash);
            $msg_index = 0;
            if(!$logs_raw) {
                echo("\ntransaction: $tr->hash\n");
                throw new \Exception('Transaction log is not found');
            }
            $logs = json_decode($logs_raw->raw_log,1);
            $previous_tr_logs['hash'] = $tr->hash;
            $previous_tr_logs['logs'] = parseLogs($logs);
            $previous_tr_logs['pool'] = [$dataHash];
        } elseif(in_array($dataHash,$previous_tr_logs['pool'])){
            echo "8";//"[repeatTx]";
            continue;
        } else {
            $previous_tr_logs['pool'][]=$dataHash;
            $msg_index++;
        }

        if (!array_key_exists($msg_index,$previous_tr_logs['logs'])){
            var_dump(json_encode($trs));
            var_dump($tr->hash,$previous_tr_logs,$logs_raw,$logs,$data['id']);
            throw new \Exception("Error, msg_index {$msg_index} not found in logs");
        }

        $values = $previous_tr_logs['logs'][$msg_index];
        foreach (explode(',',$values) as $value)
        {
            if (preg_match('/^(\d+?)(\D.*?$)/',$value,$amount))
            {
                $data['value'] = $amount[1];
                $data['denom'] = $amount[2];
            } else {
                echo '0'; // EMPTY REWARDS
                $data['value'] = $data['denom'] = null;
            }

            //var_dump($data); exit;
            //var_dump($previous_tr_logs); //exit;

            $c::makeUpsertBinds($stmt,$data);
            $stmt->execute();
        }
        $data = [];
        echo '.';
    }
    $last_id = @$trs[count($trs)-1]->id;
    //exit;

} while(count($trs)==$api->api_limit);

function parseLogs($logs)
{
    $parsed_logs = [];
    $bias = 0; // for fix cases when logs has other type than "withdraw_delegator_reward"
    // case as B31B9291B0DE662D0D79521E39F0EF66434FE2B1FA1A0E18CB5CF80F4E2945CE transaction

    global $message_types;

    foreach ($logs as $log)
    {
        $log_has_rewards = false;
        $events = array_column($log['events'],'attributes','type');
        $message = array_column($events['message'],'value','key');
        if(in_array($message['action'],$message_types))
        {
            if(!@$events['transfer']){
                $parsed_logs[$log['msg_index']+$bias]  = null;
                continue;
            }
            foreach($events['transfer'] as $attr)
            {
                if($attr['key']=='amount'){
                    $parsed_logs[$log['msg_index']+$bias] = @$attr['value'];
                    $log_has_rewards = true;
                    break;
                }
            }
        }
        if(!$log_has_rewards) $bias--;
    }
    return $parsed_logs;
}


function dataHash($data,$keys=['wallet','validator','tr_hash'])
{
    $hash = '';
    foreach ($keys as $key){$hash.=mb_strtolower($data[$key]);}
    return md5($hash);
}