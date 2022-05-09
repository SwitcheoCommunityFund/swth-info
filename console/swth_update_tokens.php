<?php


include_once __DIR__ . '/vendor/autoload.php';
include_once __DIR__ . '/swth_api.php';
include_once __DIR__ . '/connection.php';
include_once __DIR__ . '/common.php';


errorReaction();

$c = new Connection();
$db = $c->db;

$table_name = 'tokens';


$api = new SwitcheoApiClient();


$enums = $c->getEnums();


$query = $c::makeUpsertQuery($table_name,['blockchain','denom'],['name', 'denom', 'blockchain', 'decimals', 'chain_id', 'originator', 'asset_id', 'lock_proxy_hash']);
$stmt = $db->prepare($query);


$tokens = $api->getTokens();

foreach ($tokens as $token)
{
    //var_dump($validator);
    $data = [
        'name'            => $token->name,
        'denom'           => $token->denom,
        'blockchain'      => $token->blockchain,
        'decimals'        => $token->decimals,
        'chain_id'        => $token->chain_id,
        'originator'      => $token->originator,
        'asset_id'        => $token->asset_id,
        'lock_proxy_hash' => $token->lock_proxy_hash
    ];

    checkEnums($enums,$data,['denom','blockchain']);

    $c::makeUpsertBinds($stmt,$data);
    $stmt->execute();
    echo '.';
}