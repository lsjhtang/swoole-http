<?php
namespace Core\annotationhandlers;

use Core\annotations\Redis;
use Core\BeanFactory;
use Core\init\DecoratorCollector;
use Core\lib\RedisHelper;


return [
    Redis::class=>function(\ReflectionMethod $method,$instance,$self){
        $d_collector=BeanFactory::getBean(DecoratorCollector::class);
        $key=get_class($instance)."::".$method->getName();
        $d_collector->dSet[$key]=function($func){ //收集装饰器 放入 装饰器收集类

            return function($params) use($func){
                      /*RedisHelper::set("name",'1111');
                echo  RedisHelper::get("name").PHP_EOL;*/

                return call_user_func($func,$params);
            };
        };
        return $instance;
    }

];