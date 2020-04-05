<?php
namespace Core\annotationhandlers;

use Core\annotations\DB;
use Core\BeanFactory;
use Core\init\MyDB;

return [
    DB::class=>function(\ReflectionProperty $property, $instance, $self){
        $mydb_bean = BeanFactory::getBean(MyDB::class);
        $property->setAccessible(true);
        $property->setValue($instance,$mydb_bean);
        return $instance;
    }
];