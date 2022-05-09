<?php


include_once __DIR__ . '/vendor/autoload.php';
include_once __DIR__ . '/swth_api.php';
include_once __DIR__ . '/connection.php';

errorReaction();

$c = new Connection();
$db = $c->db;


$res = $db->query("select coalesce(max(id),0) as last_id from rewards")->fetch(\PDO::FETCH_OBJ);
$last_id = $res->last_id;

$api = new SwitcheoApiClient();
$api->api_limit = 100;

$check_interval = new DateInterval('P30D');

$query = $c::makeNoUpsertQuery('rewards',['id', 'wallet', 'validator', 'date', 'value', 'denom']);
$stmt = $db->prepare($query);

$previous_tr_logs = [
    'hash'=>0,
    'logs'=>[]
];

$msg_index = 0;

do {
    $trs = $api->getRewards($last_id);
    if(!$trs) {
        echo("\n-Last id is: $last_id \n-Transactions not found after\n");
        if($trs===false) throw new \Exception('Transactions load error');
        return;
    }
    foreach ($trs as $tr)
    {
        if($tr->code!=0){echo 'x'; continue;} // FAIL REWARDS
        $tr_msg = json_decode($tr->msg);
        $data = [
            'id'    =>  $tr->id,
            'wallet'=>  $tr->address,
            'validator'=> $tr_msg->validator_address,
            'date'  =>  $tr->block_time
        ];

        if(isset($tr_msg->amount))
        {
            $data['value'] = $tr_msg->amount->amount;
            $data['denom'] = $tr_msg->amount->denom;
        } else {
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
            } else $msg_index++;

            if (!array_key_exists($msg_index,$previous_tr_logs['logs'])){
                var_dump($tr->hash,$previous_tr_logs,$logs_raw,$logs);
                throw new \Exception('Error, msg_index not found in logs');
            }

            $value = $previous_tr_logs['logs'][$msg_index];
            if (preg_match('/^(\d+?)(\D.*?$)/',$value,$amount))
            {
                $data['value'] = $amount[1];
                $data['denom'] = $amount[2];
            } else {
                echo '0'; // EMPTY REWARDS
                $data['value'] = $data['denom'] = null;
            }
        }

        //var_dump($data);
        //var_dump($previous_tr_logs); //exit;

        $c::makeUpsertBinds($stmt,$data);
        $stmt->execute();
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

    foreach ($logs as $log)
    {
        $log_has_rewards = false;
        foreach ($log['events'] as $event){
            if($event['type']!='withdraw_rewards') continue;
            else {
                $log_part = array_column($event['attributes'],'value','key');
                //$parsed_logs[$log_part['validator']] = @$log_part['amount'];
                $parsed_logs[$log['msg_index']+$bias] = @$log_part['amount'];
                $log_has_rewards = true;
                break;
            }
        }
        if(!$log_has_rewards) $bias--;
    }
    return $parsed_logs;
}