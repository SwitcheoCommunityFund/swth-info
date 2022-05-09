<?php

namespace app\helpers;

use app\models\Tokens;
use app\models\Markets;

class ViewCommon {

    function getDenoms(){
        return self::getEnums('denom');
    }

    function getBlockchains(){
        return self::getEnums('blockchain');
    }

    function getTransferTypes(){
        return self::getEnums('ext_transfer_type');
    }

    function getTransactionTypes(){
        return self::getEnums('transaction_type');
    }

    function getTokens()
    {
        $tokens = Tokens::find()->asArray()->all();
        $prep=[];foreach ($tokens as $token){$prep[$token['denom']]=$token;}
        return $prep;
    }

    function getMarkets()
    {
        $markets = Markets::find()->asArray()->all();
        $prep=[];foreach ($markets as $market){$prep[$market['name']]=$market;}
        return $prep;
    }

    private static function getEnums($enum_name)
    {
        $enums = \Yii::$app->db->createCommand("
            select n.nspname as enum_schema,
                t.typname as enum_name,
                e.enumlabel as enum_value
            from pg_type t
               join pg_enum e on t.oid = e.enumtypid
               join pg_catalog.pg_namespace n ON n.oid = t.typnamespace
            where t.typname = '$enum_name'
        ")->queryAll();

        return array_column($enums,'enum_value','enum_value');
    }
}