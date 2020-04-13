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
        $d_collector->dSet[$key]=function($func) use($self) { //收集装饰器 放入 装饰器收集类
            return function($params) use($func,$self){
                /** @var $self Redis */
                if ($self->key != '') {
                    $keys = $self->key;
                    $get_from_redis = RedisHelper::get($keys);
                    if ($get_from_redis) {
                        return $get_from_redis;
                    } else {
                        $get_date = call_user_func($func, ...$params);
                        RedisHelper::set($keys, json_encode($get_date));
                        return $get_date;
                    }
                }

                return call_user_func($func, ...$params);
            };
        };
        return $instance;
    }

];