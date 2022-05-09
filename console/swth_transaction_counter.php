<?php


include_once __DIR__ . '/vendor/autoload.php';
include_once __DIR__ . '/swth_api.php';
include_once __DIR__ . '/connection.php';
include_once __DIR__ . '/common.php';

errorReaction();


$exclude_transaction_types = [
    'create_oracle_vote'
];

$c = new Connection();
$db = $c->db;



$api = new SwitcheoApiClient();
$api->api_limit = 100;

/*$query = 'INSERT INTO "transactions_count" as c (date, tr_type, count)
          VALUES (:vdate, :vtr_type, 1) 
          ON CONFLICT (date, tr_type) DO UPDATE 
          SET count=c.count+1';*/

$query = 'INSERT INTO "transactions_count" as c (date, tr_type, count) 
          VALUES (:vdate, :vtr_type, 1) 
          ON CONFLICT (date, tr_type) DO UPDATE 
          SET count=c.count+1';

$stmt = $db->prepare($query);

$enums = $c->getEnums();




$transactions_for_check = arrayExclude($enums['transaction_type'],$exclude_transaction_types);

$last_hash = getDynamicParam("last_tr_hash_reload","transaction_counter");
//$last_hash = getDynamicParam("last_tr_hash","transaction_counter");
if($last_hash===null){
    $last_id = 0;
} else {
    $tr = $api->getTransaction($last_hash);
    $last_id = (int)@$tr->id;
}


$pool=[];

do {
    $trs = $api->getTransactions([
        'after_id'   =>  $last_id,
        'order_by'   =>  'asc',
        'limit'      =>  $api->api_limit,
        'msg_type'   =>  implode(',',$transactions_for_check),
        'pagination' =>  'true',
    ]);

    //var_dump('TRS:',substr(print_r($trs,1), 0, 200));

    if(!$trs) {
        echo("\n-Last id is: $last_id \n-Transactions not found after\n");
        if($trs===false) throw new \Exception('Transactions load error');
        return;
    }
    $db->beginTransaction();
    foreach ($trs->data as $tr)
    {
        if($tr->code!=0){echo 'x'; continue;} // FAIL TRANSACTION
        //$tr_msg = json_decode($tr->msg);

        if(in_array($tr->hash,$pool)) {
            echo '0'; continue;
        } else {$pool[]=$tr->hash;}

        $data = [
            'date'      => mb_substr($tr->block_time,0,10),
            'tr_type'   => $tr->msg_type,
        ];

        if(checkEnums($enums,$data,['tr_type'=>'transaction_type'])){
            //setDynamicParam("last_tr_hash","transaction_counter",$tr->hash);
            setDynamicParam("last_tr_hash_reload","transaction_counter",$tr->hash);
            $db->commit();
            $db->beginTransaction();
        }

        $c::makeUpsertBinds($stmt,$data);
        $stmt->execute();
        $data = [];
        echo '.';
    }
    //setDynamicParam("last_tr_hash","transaction_counter",$tr->hash);
    setDynamicParam("last_tr_hash_reload", "transaction_counter", $tr->hash);
    $db->commit();
    $last_id = @$trs->data[count($trs->data)-1]->id;
    //exit;
    if(count($pool)>=$api->api_limit*2){
        $pool = array_slice($pool,$api->api_limit-1);
    }
    usleep(300);

} while(count($trs->data)==$api->api_limit);

function dataHash($data,$keys=['tr_type','tr_hash'])
{
    $hash = '';
    foreach ($keys as $key){$hash.=mb_strtolower($data[$key]);}
    return md5($hash);
}

function arrayExclude($array,$exclude)
{
    foreach ($exclude as $v){
        unset($array[array_search($v,$array)]);
    }
    return $array;
}