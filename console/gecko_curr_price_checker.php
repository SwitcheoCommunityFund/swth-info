<?php


include_once __DIR__ . '/vendor/autoload.php';
include_once __DIR__ . '/gecko_api.php';
include_once __DIR__ . '/connection.php';
include_once __DIR__ . '/common.php';


$c = new Connection();
$db = $c->db;


$updateList = $db->query("
    select
          distinct coin_gecko_id as token
    from tokens 
    where coin_gecko_id is not null
",PDO::FETCH_OBJ);

$api = new GeckoApiClient();

$query = $c::makeUpsertQuery('token_price_now',['id','currency'],['id','date', 'currency', 'current_price', 'market_cap', 'total_volume']);

$stmt = $db->prepare($query);


foreach ($updateList as $token)
{
    echo "\nc:{$token->token} - ";

    $price = $api->getLastTokenPrice($token->token);

    foreach ($price->market_data->current_price as $currency=>$value)
    {
        $data=[
            'id'            =>$price->id,
            'date'          =>$price->market_data->last_updated,
            'currency'      =>$currency,
            'current_price' =>$value,
            'market_cap'    =>@$price->market_data->market_cap->{$currency},
            'total_volume'  =>@$price->market_data->total_volume->{$currency}
        ];

        $c::makeUpsertBinds($stmt,$data);
        $stmt->execute();
        $data = [];
        echo '.';
    }
    sleep(1);
}