<?php


include_once __DIR__ . '/vendor/autoload.php';
include_once __DIR__ . '/swth_api.php';
include_once __DIR__ . '/connection.php';
include_once __DIR__ . '/common.php';

errorReaction();


$c = new Connection();
$db = $c->db;
$table = 'sends';


$api = new SwitcheoApiClient();
$api->api_limit = 100;

$last_id = getLastId($table);
echo "\n[ LAST ID IS: $last_id ]\n\n";

$check_interval = new DateInterval('P30D');

$query = $c::makeNoUpsertQuery($table,['id', 'tr_hash', 'from', 'to', 'date', 'denom','amount']);

$stmt = $db->prepare($query);

do {
    $trs = $api->getSends($last_id);
    foreach ($trs as $tr)
    {
        if($tr->code!=0){echo 'x'; continue;} // FAIL SEND
        $tr_msg = json_decode($tr->msg);
        $data = [
            'id'       =>  $tr->id,
            'tr_hash'  =>  $tr->hash,
            'from'     =>  $tr->address,
            'to'       =>  $tr_msg->to_address,
            'date'     =>  $tr->block_time
        ];

        foreach ($tr_msg->amount as $amount)
        {
            $data['amount'] =  $amount->amount;
            $data['denom']  =  $amount->denom;

            $c::makeUpsertBinds($stmt,$data);
            $stmt->execute();
        }

        $data = [];
        echo '.';
    }
    $last_id = @$trs[count($trs)-1]->id;

} while(count($trs)==$api->api_limit);