<?php


function checkEnums(&$enums,$data,$names)
{
    global $db;
    $add=false;
    foreach ($names as $field => $enum_name)
    {
        $data_field = is_numeric($field)?$enum_name:$field;
        $enum_value = @$data[$data_field];

        if(!$enum_value)         throw new Exception('Enum name is not found in data array');
        if(!@$enums[$enum_name]) throw new Exception('Enum name is not found in enums array');

        if(!in_array($enum_value,$enums[$enum_name])){
            $db->exec("alter type \"$enum_name\" add value ".$db->quote($enum_value,PDO::PARAM_STR));
            $enums[$enum_name][]=$enum_value;
            echo "+({$enum_value})";
            $add = true;
        }
    }
    return $add;
}


function getLastId($table, $hash_name='tr_hash')
{
    global $db;
    global $api;

    $res = $db->query("select \"{$hash_name}\" as last_hash from \"{$table}\" order by id desc limit 1")->fetch(\PDO::FETCH_OBJ);

    //var_dump('lastId',$res);

    if(@$res->last_hash){
        $last_tr = $api->getTransaction($res->last_hash);
        $last_id = $last_tr->id;
    } else $last_id = 0;

    return $last_id;
}


function getTableLastId($table, $id_name='id')
{
    global $db;

    $res = $db->query("select \"{$id_name}\" as last_id from \"{$table}\" order by id desc limit 1")->fetch(\PDO::FETCH_OBJ);

    return (int)@$res->last_id;
}

function errorReaction()
{

    try {
        $_memoryReserve = str_repeat('x', 256);

        /*$mailer_login = 'switcheo.community.fund@gmail.com';
        $mailer_name = 'SWTH.INFO';
        $mailer_password = 'CommunityFund2020';
        $mailer_receivers = ['auefim@gmail.com', 'forbusinessideas@gmail.com'];

        $transport = (new Swift_SmtpTransport('smtp.gmail.com', 465, 'ssl'))
            ->setUsername($mailer_login)
            ->setPassword($mailer_password);

        $mailer = new Swift_Mailer($transport);

        $message = (new Swift_Message('[SWTH API CHECKER ERROR]'))
            ->setFrom([$mailer_login => $mailer_name])
            ->setTo($mailer_receivers);*/

        $exceptionHandler = function ($errno ,  $errstr = null ,  $errfile  = null,  $errline  = null,  $errcontext = null) use (&$_memoryReserve) /*use ($mailer, $message)*/
        {
            unset($_memoryReserve);
            if($errno instanceof Exception){
                $exception = $errno;
                $err_message = $exception->getMessage();
                $file = basename($exception->getFile(), '.php');
                $line = $exception->getLine();
                $trace = $exception->getTraceAsString();
            } elseif($errno instanceof Error) {
                $error = $errno; //(new Error)->
                $err_message = $error->getMessage();
                $file = basename($error->getFile());
                $line = $error->getLine();
                $trace = $error->getTraceAsString();
            } else {
                $err_message = $errstr;
                $file = basename($errfile);
                $line = $errline;
                $trace = null;
            }
            system('node /var/www/html/swth_js/sendmail.js '.base64_encode(json_encode([
                    'body'=>"Error: $err_message (in $file on $line)",
                    'subj'=>'[SWTH API CHECKER ERROR]',
                ])));
            var_dump('Error:',$err_message,['file'=>$file, 'line'=>$line, 'trace'=>$trace]);

            /*$message->setBody("Error: $err_message (in $file on $line)");
            $result = $mailer->send($message);*/
        };

        set_exception_handler($exceptionHandler);
        //set_error_handler($exceptionHandler,error_reporting());

        register_shutdown_function(function () use ($exceptionHandler) {
            $error = error_get_last();
            if ($error !== NULL && $error['type'] === E_ERROR) {
                call_user_func($exceptionHandler, new ErrorException($error['type'], $error['message'], $error['file'], $error['line']));
            }
        });

    }catch (Exception $e){
        var_dump('Connect error handle mailer',$e);
    }
}

function getDynamicParam($name,$system)
{
    global $db;
    $stmt = $db->prepare('
        SELECT param 
        FROM dynamic_params 
        WHERE "name"=? and "system"=?
    ');
    $stmt->execute([$name,$system]);
    $data = $stmt->fetch(PDO::FETCH_OBJ);
    return @$data->param;
}

function setDynamicParam($name,$system,$param)
{
    global $db;
    $stmt = $db->prepare('
        INSERT INTO dynamic_params ("name", "system", date_change, param) 
        VALUES (:name,:system,current_date,:param) 
        ON CONFLICT ("name", "system") DO UPDATE
        SET param=:param 
    ');
    $stmt->bindValue(':name',$name);
    $stmt->bindValue(':system',$system);
    $stmt->bindValue(':param',$param);
    return $stmt->execute();
}