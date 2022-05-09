<?php


require_once __DIR__ . '/../common.php';
require_once __DIR__ . '/../connection.php';
require_once "carbon_api.php";

//require_once "./txParsers/SkipList.php";

$api = new CarbonApiClient();

//$start_block = getDynamicParam('last_height','carbon_block_parser');

//$last_block = $start_block;
$last_block = 21270909;

$skip_tx_types = ['Switcheo.carbon.oracle.MsgCreateVote'];

$c = new Connection();
$db = $c->db;

$table_name = 'carbon_txs';

$query = $c::makeUpsertQuery($table_name, ['height','hash','index'], ['height','hash','index','type','log']);
$stmt = $db->prepare($query);

while(1){
    $result = $api->TxSearch($last_block)->result;
    $height_txs = $result->txs;
    if($result->total_count < 1) echo "-";
    foreach ($height_txs as $tx)
    {
        if(($tx_state = $tx->tx_result->code) != 0){echo "x"; continue;}
        $tx_logs = transformTxLog(json_decode($tx->tx_result->log, true));
        $tx_types = parseTxType($tx->tx_result->data);
        $tx_hash = $tx->hash;
        $tx_height = $tx->height;
        if(count($tx_logs) != count($tx_types)) {
            var_dump($tx_types, $tx_height, $tx_hash);
            throw new Exception("tx logs more then tx types");
        }
        foreach ($tx_types as $ind => $tx_type) {
            $tx_type = trim($tx_type);
            if (in_array($tx_type, $skip_tx_types)){
                /* skip txs by declared types in @skip_tx_types */
                echo "o"; continue;
            }
            $data = [
                'height' => $tx_height,
                'hash' => $tx_hash,
                'index' => $ind,
                'type' => $tx_type,
                'log' => json_encode($tx_logs[$ind]),
            ];
            $c::makeUpsertBinds($stmt,$data);
            $stmt->execute();
            echo ".";
        }
    }
    $last_block += 1;
}

function transformTxLog($logs_events){
    $logs = [];
    foreach ($logs_events as $log)
    {
        $events = array_column($log['events'],'attributes','type');

        foreach ($events as &$event){
            $event = array_column($event,'value','key');
            foreach ($event as &$item){
                $decode = @json_decode($item);
                $item = $decode?$decode:$item;
            }
        }

        $logs[]=$events;
    }
    return($logs);
}

function parseTxType($tx_data){
    preg_match_all('/^.*?\/(.{6,}?)$/m',base64_decode($tx_data),$matches);
    return $matches[1];
}