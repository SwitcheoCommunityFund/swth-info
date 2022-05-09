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



/* ########################## GET MARKETS ###################### */

echo "\n\nGet Markets:\n";
//$last_id = getLastId('proposals');

$query = $c::makeNoUpsertQuery('markets',['type', 'name', 'display_name', 'description', 'market_type', 'base', 'base_name', 'base_precision', 'quote', 'quote_name', 'quote_precision', 'lot_size', 'tick_size', 'min_quantity', 'maker_fee', 'taker_fee', 'risk_step_size', 'initial_margin_base', 'initial_margin_step', 'maintenance_margin_ratio', 'max_liquidation_order_ticket', 'max_liquidation_order_duration', 'impact_size', 'mark_price_band', 'last_price_protected_band', 'index_oracle_id', 'expiry_time', 'is_active', 'is_settled', 'closed_block_height', 'created_block_height']);
$stmt = $db->prepare($query);

$params = [
    //'msg_type'=>'get_markets',
    //'after_id'=>$last_id,
    //'limit'=>$api->api_limit,
    //'order_by'=>'asc'
];

do {
    $trs = $api->getMarkets($params);
    foreach ($trs as $tr)
    {
        //if($tr->code!=0){echo 'x'; continue;} // FAIL TRANSACTION
        //$tr_msg = json_decode($tr->msg);

        //$logs = $api->getTransactionLogs($tr->hash);
        //$log = json_decode($logs->raw_log,1)[0];

        //$events = array_column($log['events'],'attributes','type');
        //$submit_params = array_column($events['submit_proposal'],'value','key');

        $data=[
            'type'                      =>  $tr->type,
            'name'                      =>  $tr->name,
            'display_name'              =>  $tr->display_name,
            'description'               =>  $tr->description,
            'market_type'               =>  $tr->market_type,
            'base'                      =>  $tr->base,
            'base_name'                 =>  $tr->base_name,
            'base_precision'            =>  $tr->base_precision,
            'quote'                     =>  $tr->quote,
            'quote_name'                =>  $tr->quote_name,
            'quote_precision'           =>  $tr->quote_precision,
            'lot_size'                  =>  $tr->lot_size,
            'tick_size'                 =>  $tr->tick_size,
            'min_quantity'              =>  $tr->min_quantity,
            'maker_fee'                 =>  $tr->maker_fee,
            'taker_fee'                 =>  $tr->taker_fee,
            'risk_step_size'            =>  $tr->risk_step_size,
            'initial_margin_base'       =>  $tr->initial_margin_base,
            'initial_margin_step'       =>  $tr->initial_margin_step,
            'maintenance_margin_ratio'  =>  $tr->maintenance_margin_ratio,
            'max_liquidation_order_ticket' =>  $tr->max_liquidation_order_ticket,
            'max_liquidation_order_duration' =>  $tr->max_liquidation_order_duration,
            'impact_size'               =>  $tr->impact_size,
            'mark_price_band'           =>  $tr->mark_price_band,
            'last_price_protected_band' =>  $tr->last_price_protected_band,
            'index_oracle_id'           =>  (int)$tr->index_oracle_id,
            'expiry_time'               =>  $tr->expiry_time,
            'is_active'                 =>  is_bool($tr->is_active)?1:0,
            'is_settled'                =>  is_bool($tr->is_settled)?1:0,
            'closed_block_height'       =>  $tr->closed_block_height,
            'created_block_height'      =>  $tr->created_block_height,
        ];
        $c::makeUpsertBinds($stmt,$data);
        try{
            $stmt->execute();
        }catch (Exception $e){
            var_dump($data);
            throw new Exception($e);
        }
        $data = [];
        echo '.';
    }
    //$params['after_id'] = @$trs[count($trs)-1]->id;

} while(count($trs)==$api->api_limit);







/* ########################## GET TRADINGS ###################### */


echo "\n\nGet Trades:\n";

$last_id = getTableLastId('trades');

$query = $c::makeNoUpsertQuery('trades',['id', 'block_created_at', 'taker_id', 'taker_address', 'taker_fee_amount', 'taker_fee_denom', 'taker_side', 'maker_id', 'maker_address', 'maker_fee_amount', 'maker_fee_denom', 'maker_side', 'market', 'price', 'quantity', 'liquidation', 'block_height']);
$stmt = $db->prepare($query);

$params = [
    //'msg_type'=>'vote',
    'after_id'=>$last_id,
    'limit'=>$api->api_limit,
    'order_by'=>'asc'
];

do {
    $trs = $api->getTrading($params);
    foreach ($trs as $tr)
    {
        //if($tr->code!=0){echo 'x'; continue;} // FAIL TRANSACTION
        //$tr_msg = json_decode($tr->msg);

        $data=[
            'id'               => $tr->id,
            'block_created_at' => $tr->block_created_at,
            'taker_id'         => $tr->taker_id,
            'taker_address'    => $tr->taker_address,
            'taker_fee_amount' => $tr->taker_fee_amount,
            'taker_fee_denom'  => $tr->taker_fee_denom,
            'taker_side'       => $tr->taker_side,
            'maker_id'         => $tr->maker_id,
            'maker_address'    => $tr->maker_address,
            'maker_fee_amount' => $tr->maker_fee_amount,
            'maker_fee_denom'  => $tr->maker_fee_denom,
            'maker_side'       => $tr->maker_side,
            'market'           => $tr->market,
            'price'            => $tr->price,
            'quantity'         => $tr->quantity,
            'liquidation'      => $tr->liquidation,
            'block_height'     => $tr->block_height,
        ];

        $c::makeUpsertBinds($stmt,$data);
        try {
            $stmt->execute();
        }catch (Exception $e){
            var_dump($data);
            throw new Exception($e);
        }
        $data = [];
        echo '.';
    }
    $params['after_id'] = @$trs[count($trs)-1]->id;

} while(count($trs)==$api->api_limit);