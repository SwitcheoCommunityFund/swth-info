<?php


include_once __DIR__ . '/vendor/autoload.php';
include_once __DIR__ . '/swth_api.php';
include_once __DIR__ . '/connection.php';
include_once __DIR__ . '/common.php';

errorReaction();

$c = new Connection();
$db = $c->db;

$table_name = 'account_info';


$res = $db->query("
    select distinct wallet from rewards
    union
    select distinct wallet from bonds
    union
    select wallet from withdrawal_wallets
    except
    select account  from account_info;
",\PDO::FETCH_ASSOC);



$api = new SwitcheoApiClient();
$api->api_limit = 100;

$query = $c::makeNoUpsertQuery($table_name,["account", "tr_first", "tr_hash", "username"]);
$stmt = $db->prepare($query);

$msg_index = 0;


foreach ($res as $row) {
    $trs = $api->getTransactions([
        'address'  => $row['wallet'],
        'limit'    => 1,
        'order_by' => 'asc'
    ]);
    if(!$trs) {
        echo("\n-Wallet is: {$row['account']} \n-Transactions not found for this wallet \n");
        if($trs===false) throw new \Exception('Transactions load error');
    }
    foreach ($trs as $tr)
    {
        $data = [
            "account"  => $row['wallet'],
            "tr_hash"  => $tr->hash,
            "tr_first" => $tr->block_time,
            "username" => @$tr->username,
        ];

        try {
            $c::makeUpsertBinds($stmt, $data);
            $stmt->execute();
        }catch (\Exception $e){
            echo "\nCurr row id is {$row['id']}\n";
            throw new \Exception($e);
        }
        $data = [];
        echo '.';
    }
    usleep(500);
}