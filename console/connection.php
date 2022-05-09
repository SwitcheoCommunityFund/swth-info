<?php


class Connection
{
    private
        $database='switcheo',
        $host='localhost',
        $port='5432',
        $user='service_manager',
        $pass='****';

    public $db;

    public function __construct($db=null)
    {
        $this->db = $this->createConnection($db);
        return $this;
    }

    private function createConnection($data_base=null)
    {
        $data_base = empty($data_base)?$this->database:$data_base;
        $pgsql = new PDO("pgsql:dbname={$data_base};host={$this->host};port={$this->port};user={$this->user};password={$this->pass}");
        $pgsql->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pgsql->setAttribute(PDO::ATTR_CASE, PDO::CASE_LOWER);
        return $pgsql;
    }

    static function makeUpsertQuery($table,$keys,$fields)
    {
        $insertfields = '"' . implode('","', $fields) . '"';
        $insertvalues = ':v' . implode(', :v', $fields);
        $insertKeys = implode(',', $keys);
        $update = '';
        $where = '';
        foreach($fields as $v){
            if(in_array($v, $keys)){
                $where .= $table . '.' . $v . '=:v' . $v . ' AND ';
                continue;
            }
            $update .= "\"$v\"" . '=:v' . $v . ',';
        }
        $update = rtrim($update, ',');
        $where = rtrim($where, ' AND ');
        return "INSERT INTO $table ($insertfields) VALUES ($insertvalues) ON CONFLICT ($insertKeys) DO UPDATE set $update WHERE $where;\n";
    }

    static function makeNoUpsertQuery($table,$fields)
    {
        $insertfields = '"' . implode('","', $fields) . '"';
        $insertvalues = ':v' . implode(', :v', $fields);
        return "INSERT INTO $table ($insertfields) VALUES ($insertvalues) ON CONFLICT DO NOTHING ;\n";
    }

    static function makeUpsertBinds(&$stmt,$values,$fields=null)
    {
        foreach($values as $key=>$value)
        {
            $param = $key;
            if($fields) $param=$fields[$key];
            $stmt->bindValue(":v$param",$value);
        }
    }

    static function makeUpsertBindsLoop(&$stmt,$values_array,$fields=null)
    {
        foreach($values_array as $values)
        {
            self::makeUpsertBinds($stmt,$values,$fields);
            $stmt->execute();
        }
    }

    function getEnums($name=null)
    {
        $where = $name?"where typename=? ":null;
        $params=[];
        if($name) $params[]=$name;
        $stmt = $this->db->prepare("
            select n.nspname as enum_schema,
                   t.typname as enum_name,
                   e.enumlabel as enum_value
            from pg_type t
               join pg_enum e on t.oid = e.enumtypid
               join pg_catalog.pg_namespace n ON n.oid = t.typnamespace
            $where
        ");
        if($params) $stmt->execute($params);
        else $stmt->execute();

        $all = $stmt->fetchAll();
        $prettify = [];
        foreach ($all as $row){
            if(!isset($prettify[$row['enum_name']])) $prettify[$row['enum_name']]=[];
            $prettify[$row['enum_name']][]=$row['enum_value'];
        }
        return $prettify;
    }

}