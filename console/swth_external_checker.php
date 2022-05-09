<?php


include_once __DIR__ . '/vendor/autoload.php';
include_once __DIR__ . '/swth_api.php';
include_once __DIR__ . '/connection.php';
include_once __DIR__ . '/common.php';

errorReaction();

$c = new Connection();
$db = $c->db;

$table_name = 'external_transfers';


$res = $db->query("
    select distinct wallet from rewards
    union
    select distinct wallet from bonds
    union 
    select wallet from withdrawal_wallets
    /*
    select * from tmp_wallets
    order by id
    */
",\PDO::FETCH_ASSOC);

$db->exec("
    delete from external_transfers where id in (
        select distinct ct.id from external_transfers st
        inner join
    (select * from external_transfers where status = 'confirming') as ct on
            st.status !='confirming'
            and ct.wallet=st.wallet
            and ct.amount=st.amount
            and ct.transfer_type=st.transfer_type
            and ct.timestamp < st.timestamp
    )
");

$enums = $c->getEnums();

$api = new SwitcheoApiClient();
$api->api_limit = 100;

$query = $c::makeUpsertQuery($table_name,['wallet','transaction_hash'],["id", "wallet", "amount", "blockchain", "denom", "fee_amount", "status", "timestamp", "transaction_hash", "transfer_type"]);
$stmt = $db->prepare($query);

$msg_index = 0;


foreach ($res as $row) {
    $trs = $api->getExternalTransfers($row['wallet']);
    if(!$trs) {
        echo("\n-Wallet is: {$row['wallet']} \n-External transfers not found for this wallet \n");
        if($trs===false) throw new \Exception('External transfers load error');
    }
    foreach ($trs as $tr)
    {
        $data = [
            "id"               => $tr->id,
            "wallet"           => $tr->address,
            "amount"           => $tr->amount,
            "blockchain"       => $tr->blockchain,
            "denom"            => $tr->denom,
            "fee_amount"       => $tr->fee_amount,
            "status"           => $tr->status,
            "timestamp"        => date('Y-m-d H:i:s.uO',$tr->timestamp),
            "transaction_hash" => $tr->transaction_hash,
            "transfer_type"    => $tr->transfer_type
        ];

        checkEnums($enums,$data,['denom','blockchain']);

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
}