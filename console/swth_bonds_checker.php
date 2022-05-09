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

$last_id = getLastId('bonds');
echo "\n[ LAST ID IS: $last_id ]\n\n";

$check_interval = new DateInterval('P30D');

$query = $c::makeNoUpsertQuery('bonds',['id', 'wallet', 'validator', 'date', 'value', 'denom','tr_hash']);

$stmt = $db->prepare($query);

do {
    $trs = $api->getUnbonds($last_id);
    foreach ($trs as $tr)
    {
        /*$date = date_create_from_format('Y-m-d\TH:i:s.u\Z',$tr->block_time);
        $unbond_date = (clone $date)->add($check_interval);
        $now_date = (new DateTime('now',(new DateTimeZone('UTC'))));
        //var_dump($tr->id,$tr->block_time,$date,$bond_date,$bond_date>$now_date);
        //echo "------------------------------------------------------------------------";
        if($unbond_date<$now_date){echo 'o'; continue;} // OLD BONDS*/
        if($tr->code!=0){echo 'x'; continue;} // FAIL BONDS
        $tr_msg = json_decode($tr->msg);
        $data=[
            'id'       =>  $tr->id,
            'wallet'   =>  $tr->address,
            'validator'=>  $tr_msg->validator_address,
            'date'     =>  $tr->block_time,
            'value'    =>  $tr_msg->amount->amount,
            'denom'    =>  $tr_msg->amount->denom,
            'tr_hash'  =>  $tr->hash
        ];

        $c::makeUpsertBinds($stmt,$data);
        $stmt->execute();
        $data = [];
        echo '.';
    }
    $last_id = @$trs[count($trs)-1]->id;

} while(count($trs)==$api->api_limit);