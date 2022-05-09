<?php


include_once __DIR__ . '/vendor/autoload.php';
include_once __DIR__ . '/swth_api.php';
include_once __DIR__ . '/connection.php';
include_once __DIR__ . '/common.php';

errorReaction();

$c = new Connection();
$db = $c->db;

$table_name = 'validators';


$api = new SwitcheoApiClient();


$query = $c::makeUpsertQuery($table_name,['address'],['address', 'name', 'details', 'wallet']);
$stmt = $db->prepare($query);


$validators = $api->getValidators();

foreach ($validators as $validator)
{
    //var_dump($validator);
    $data = [
        'address' => $validator->OperatorAddress,
        'name'    => $validator->Description->moniker,
        'details' => $validator->Description->details,
        'wallet'  => $validator->WalletAddress,
    ];
    $c::makeUpsertBinds($stmt,$data);
    $stmt->execute();
    echo '.';
}