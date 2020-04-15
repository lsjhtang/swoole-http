<?php
namespace Core\annotationhandlers;

use Core\annotations\Redis;
use Core\BeanFactory;
use Core\init\DecoratorCollector;
use Core\lib\RedisHelper;


function getKey($key,$params){//正则自动验证路由key
    $pattern = "/^#(\w+)/i";
    if (preg_match($pattern, $key, $matches)) {
        if (isset($params[$matches[1]])) {
            if (is_string($params[$matches[1]]) || is_int($params[$matches[1]])) {
                return $params[$matches[1]];
            }
        }
    }
    return $key;
}


function RedisByString(Redis $self,array $params,$func){//处理string类型的数据
    $_key = $self->prefix.getKey($self->key,$params); //缓存key
    $getFromRedis = RedisHelper::get($_key);
    if($getFromRedis){ //缓存如果有，直接返回
        return $getFromRedis;
    }else{ //缓存没有，则直接执行原控制器方法，并返回
        $getData=call_user_func($func,...$params);
        if($self->expire>0){//过期时间
            RedisHelper::setex($_key,$self->expire,json_encode($getData));
        } else {
            RedisHelper::set($_key,json_encode($getData));
        }
        return $getData;
    }
}

function RedisByHash(Redis $self,array $params,$func){//处理hash类型的数据
    $_key = $self->prefix.getKey($self->key,$params); //缓存key
    $getFromRedis = RedisHelper::hgetall($_key);
    if($getFromRedis){ //缓存如果有，直接返回
        if ($self->incr != '') {
            RedisHelper::hIncrBy($_key, $self->incr, 1);
        }
        return $getFromRedis;
    }else{ //缓存没有，则直接执行原控制器方法，并返回
        $getData=call_user_func($func,...$params);
        if (is_array($getData) || is_object($getData)) {
            if(is_object($getData)){//如果是对象，转换成数组
                $getData=json_decode(json_encode($getData),1);
            }
            $keys=implode("",array_keys($getData));
            if(preg_match("/^\d+$/",$keys)){
                foreach($getData as $k => $data){
                    RedisHelper::hmset($self->prefix.getKey($self->key,$data).$k,$data);
                }
            }else{
                RedisHelper::hmset($_key,$getData);
            }

        }
        return $getData;
    }
}



return [
    Redis::class=>function(\ReflectionMethod $method,$instance,$self){
        $d_collector=BeanFactory::getBean(DecoratorCollector::class);
        $key=get_class($instance)."::".$method->getName();
        $d_collector->dSet[$key]=function($func) use($self) { //收集装饰器 放入 装饰器收集类
            return function($params) use($func,$self){

                /** @var $self Redis */
                if($self->key!=""){ //处理缓存
                    switch($self->type){
                        case "string":
                            return RedisByString($self,$params,$func);
                        case "hash":
                            return RedisByHash($self,$params,$func);
                        default:
                            return call_user_func($func,...$params);
                    }
                }
                return call_user_func($func,...$params);
            };
        };
        return $instance;
    }

];