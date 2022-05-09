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



/* ########################## GET PROPOSALS ###################### */

$last_id = getLastId('proposals');

$query = $c::makeNoUpsertQuery('proposals',['id', 'proposer', 'tr_hash', 'date', 'title', 'description', 'proposal_id', 'proposal_type']);
$stmt = $db->prepare($query);

$params = [
    'msg_type'=>'submit_proposal',
    'after_id'=>$last_id,
    'limit'=>$api->api_limit,
    'order_by'=>'asc'
];

do {
    $trs = $api->getTransactions($params);
    foreach ($trs as $tr)
    {
        if($tr->code!=0){echo 'x'; continue;} // FAIL TRANSACTION
        $tr_msg = json_decode($tr->msg);

        $logs = $api->getTransactionLogs($tr->hash);
        $log = json_decode($logs->raw_log,1)[0];

        $events = array_column($log['events'],'attributes','type');
        $submit_params = array_column($events['submit_proposal'],'value','key');

        $data=[
            'id'            =>  $tr->id,
            'proposer'      =>  $tr->address,
            'tr_hash'       =>  $tr->hash,
            'date'          =>  $tr->block_time,
            'title'         =>  @$tr_msg->content->title,
            'description'   =>  @$tr_msg->content->description,
            'proposal_id'   =>  $submit_params['proposal_id'],
            'proposal_type' =>  $submit_params['proposal_type']
        ];

        $c::makeUpsertBinds($stmt,$data);
        $stmt->execute();
        $data = [];
        echo '.';
    }
    $params['after_id'] = @$trs[count($trs)-1]->id;

} while(count($trs)==$api->api_limit);







/* ########################## GET VOTES ###################### */


$last_id = getLastId('votes');

$query = $c::makeNoUpsertQuery('votes',['id', 'voter', 'proposal_id', 'tr_hash', 'date', 'option']);
$stmt = $db->prepare($query);

$params = [
    'msg_type'=>'vote',
    'after_id'=>$last_id,
    'limit'=>$api->api_limit,
    'order_by'=>'asc'
];

do {
    $trs = $api->getTransactions($params);
    foreach ($trs as $tr)
    {
        if($tr->code!=0){echo 'x'; continue;} // FAIL TRANSACTION
        $tr_msg = json_decode($tr->msg);

        $data=[
            'id'            =>  $tr->id,
            'voter'         =>  $tr->address,
            'proposal_id'   =>  $tr_msg->proposal_id,
            'tr_hash'       =>  $tr->hash,
            'date'          =>  $tr->block_time,
            'option'        =>  $tr_msg->option
        ];

        $c::makeUpsertBinds($stmt,$data);
        $stmt->execute();
        $data = [];
        echo '.';
    }
    $params['after_id'] = @$trs[count($trs)-1]->id;

} while(count($trs)==$api->api_limit);