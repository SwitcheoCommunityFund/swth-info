<?php


include_once __DIR__ . '/vendor/autoload.php';
include_once __DIR__ . '/swth_api.php';
include_once __DIR__ . '/connection.php';
include_once __DIR__ . '/common.php';

errorReaction();

$c = new Connection();
$db = $c->db;

$api = new SwitcheoApiClient();
$api->api_limit = 100;

$last_id = getLastId('delegates');
echo "\n[ LAST ID IS: $last_id ]\n\n";

$query = $c::makeNoUpsertQuery('delegates',['id', 'wallet', 'validator', 'date', 'value', 'denom', 'tr_hash']);
$stmt = $db->prepare($query);

do {
    $trs = $api->getDelegates($last_id);
    foreach ($trs as $tr)
    {
        if($tr->code!=0){echo 'x'; continue;} // FAIL DELEGATES
        $tr_msg = json_decode($tr->msg);
        $data=[
            'id'        =>  $tr->id,
            'wallet'    =>  $tr->address,
            'validator' =>  $tr_msg->validator_address,
            'date'      =>  $tr->block_time,
            'tr_hash'   =>  $tr->hash
        ];
        if($tr->msg_type=='delegate'){
            $data['value'] = $tr_msg->amount->amount;
            $data['denom'] = $tr_msg->amount->denom;
        } else {
            $data['value'] = $tr_msg->value->amount;
            $data['denom'] = $tr_msg->value->denom;
        }

        $c::makeUpsertBinds($stmt,$data);
        $stmt->execute();
        $data = [];
        echo '.';
    }
    $last_id = @$trs[count($trs)-1]->id;

} while(count($trs)==$api->api_limit);