<?php

require_once __DIR__. '/vendor/autoload.php';
use App\annotations\Value;

use App\core\ClassFactory;
use App\test\MyRedis;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;


/**
 * Class MyTest
 */
class MyTest {

}

//注册命名空间
AnnotationRegistry::registerAutoloadNamespace("App\annotation");

$result = ClassFactory::ScanBeans(__DIR__.'/app/test','App\\test');
$myredis = ClassFactory::getBean(\App\test\MyUsers::class);
var_dump($myredis);
exit();

//$result = ClassFactory::loadClass(MyRedis::class);

