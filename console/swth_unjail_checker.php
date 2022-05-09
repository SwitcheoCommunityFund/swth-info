<?php


include_once __DIR__ . '/vendor/autoload.php';
include_once __DIR__ . '/swth_api.php';
include_once __DIR__ . '/connection.php';
include_once __DIR__ . '/common.php';

errorReaction();

$c = new Connection();
$db = $c->db;

$table_name = 'unjails';

$last_id = getTableLastId('unjails');

$api = new SwitcheoApiClient();


$query = $c::makeNoUpsertQuery($table_name,['id','wallet','validator','date','tr_hash']);
$stmt = $db->prepare($query);


$trs = $api->getUnjails($last_id);
{
    foreach ($trs as $tr)
    {
        //var_dump($validator);
        if($tr->code!=0){echo 'x'; continue;} // FAIL UNJAILS
        $tr_msg = json_decode($tr->msg);
        $data = [
            'id'        => $tr->id,
            'wallet'    => $tr->address,
            'validator' => $tr_msg->address,
            'date'      => $tr->block_time,
            'tr_hash'   => $tr->hash,
        ];
        $c::makeUpsertBinds($stmt,$data);
        $stmt->execute();
        echo '.';
    }
    $last_id = @$trs[count($trs)-1]->id;
} while(count($trs)==$api->api_limit);