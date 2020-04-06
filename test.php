<?php

require_once (__DIR__.'/vendor/autoload.php');
require_once (__DIR__.'/app/config/define.php');

$db = new \Core\init\MyDB();
$test = $db->table('test')->get();
foreach ($test as $item) {
   echo  $item->age;
}