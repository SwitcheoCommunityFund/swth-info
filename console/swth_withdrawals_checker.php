<?php


include_once __DIR__ . '/vendor/autoload.php';
include_once __DIR__ . '/swth_api.php';
include_once __DIR__ . '/connection.php';
include_once __DIR__ . '/common.php';


errorReaction();

$c = new Connection();
$db = $c->db;

$table_name = 'withdrawal_wallets';

$last_id = $db->query("select coalesce(max(id),0) as lid from {$table_name}")->fetch(\PDO::FETCH_OBJ)->lid;

$api = new SwitcheoApiClient();
$api->api_limit = 100;

$query = $c::makeUpsertQuery($table_name,['wallet'],["id", "wallet"]);
$stmt = $db->prepare($query);

$msg_index = 0;


do {
    $trs = $api->getWithdrawals($last_id);
    if(!$trs) {
        echo("\n-Last id: {$last_id} \n-Withdrawals not found after \n");
        if($trs===false) throw new \Exception('Withdrawals load error');
    }
    foreach ($trs as $tr)
    {
        if($tr->code!=0){echo 'x'; continue;} // FAIL
        $tr_msg = json_decode($tr->msg);
        $data=[
            'id'    =>  $tr->id,
            'wallet'=>  $tr->address,
        ];

        $c::makeUpsertBinds($stmt,$data);
        $stmt->execute();
        $data = [];
        echo '.';
    }
    $last_id = @$trs[count($trs)-1]->id;

} while(count($trs)==$api->api_limit);