<?php


include_once __DIR__ . '/vendor/autoload.php';
include_once __DIR__ . '/gecko_api.php';
include_once __DIR__ . '/connection.php';
include_once __DIR__ . '/common.php';


$c = new Connection();
$db = $c->db;


$api = new GeckoApiClient();
$api->api_limit = 100;

$min_date_start = '01-01-2020';

$updateList = $db->query("
    select
           coalesce(max(date),'{$min_date_start}') as last_date,
           current_date - coalesce(max(date),'{$min_date_start}') + 2 as load_days,
           coalesce(th.id,coin_gecko_id) as token
    from token_history th
    full join tokens t on th.id = t.coin_gecko_id and coin_gecko_id is not null
    where t.coin_gecko_id is not null
    group by th.id,coin_gecko_id;
",PDO::FETCH_OBJ);


$query = $c::makeUpsertQuery('token_history',['id', 'date', 'currency'],['id','date', 'currency', 'current_price', 'market_cap', 'total_volume']);

$stmt = $db->prepare($query);

foreach ($updateList as $token)
{
    echo "\nc:{$token->token} - ";
    $last_date = strtotime($token->last_date);
    $today = strtotime(date('Y-m-d'));
    for($date=$last_date; $date<=$today; $date+=3600*24)
    {
        //echo "\n".date('d-m-Y',$date);
        $hist = $api->getTokenHistory($token->token,date('d-m-Y',$date));
        //var_dump($hist); exit;
        if(empty($hist->market_data)){ echo 'o'; sleep(1); continue; }
        foreach ($hist->market_data->current_price as $currency=>$value)
        {
            $data=[
                'id'            =>$token->token,
                'date'          =>date('Y-m-d',$date),
                'currency'      =>$currency,
                'current_price' =>$value,
                'market_cap'    =>@$hist->market_data->market_cap->{$currency},
                'total_volume'  =>@$hist->market_data->total_volume->{$currency}
            ];

            $c::makeUpsertBinds($stmt,$data);
            $stmt->execute();
            $data = [];
            echo '.';
        }
        sleep(1);
    }
    echo "\n";
}