<?php
error_reporting(E_ALL^E_NOTICE);
require_once __DIR__."/vendor/autoload.php";
require_once __DIR__."/app/config/define.php";

function newDB()
{
    global $GLOBAL_CONFIGS;
    $default=$GLOBAL_CONFIGS["db"]["default"];
    $dsn="";
    {
        $driver=$default["driver"];
        $host=$default["host"];
        $dbname=$default['database'];
        $username=$default["username"];
        $password=$default["password"];
        $dsn="$driver:host=$host;dbname=$dbname";
    }
    $pdo=new \PDO($dsn,$username,$password);
    return $pdo;
}


use Illuminate\Database\Capsule\Manager as lvdb;
$lvdb=new lvdb();

$database = [
    'driver'    => 'mysql',
];
$lvdb->addConnection($database,"default");
$lvdb->setAsGlobal();
$lvdb->bootEloquent();

$lvdb->getConnection("default")->setPdo(newDB());


$lvdb::connection("default")->beginTransaction();

$lvdb::connection("default")->table("test")->where('id',2)->update(
    ["user_name"=>"11111111111","age"=>1]
);
$lvdb::connection('default')->commit();  //或者commit







