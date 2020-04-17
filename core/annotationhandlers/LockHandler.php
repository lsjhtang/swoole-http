<?php
namespace Core\annotationhandlers;

use Core\annotations\Lock;
use Core\annotations\Redis;
use Core\BeanFactory;
use Core\init\DecoratorCollector;
use Core\lib\RedisHelper;

function getLock($self,$params){//生成锁脚本
    $script = <<<LUA
        local key = KEYS[1]
        local expire = ARGV[1]
        if redis.call('setnx',key,1)==1 then
            return redis.call('expire',key,expire)
        end
        return 0    
LUA;
    return RedisHelper::eval($script,[$self->prefix.getKey($self->key,$params),$self->expire],1);
}

function delLock($self,$params){//释放锁脚本
    $script = <<<LUA
        local key = KEYS[1]
        return redis.call('del',key)
LUA;
    return RedisHelper::eval($script,[$self->prefix.getKey($self->key,$params)],1);
}

function lock($self,$params) {//争抢所
    $retry = $self->retry;
    while ($retry-- > 0) {
        $get_lock = getLock($self,$params);
        if ($get_lock) {
            return true;
            break;
        }
        usleep(1000*200);
    }
    return  false;
}

function run($self,$params,$func) {//执行抢锁
    try {
        if (lock($self,$params)) {
            $result = call_user_func($func,...$params);//执行业务逻辑
            delLock($self,$params);
            return $result;
        }
        return false;
    }catch (\Exception $exception){
        delLock($self,$params);
        return 'false';
    }
}

return [
    Lock::class=>function(\ReflectionMethod $method,$instance,$self){
        $d_collector=BeanFactory::getBean(DecoratorCollector::class);
        $key=get_class($instance)."::".$method->getName();
        $d_collector->dSet[$key]=function($func) use($self) { //收集装饰器 放入 装饰器收集类
            return function($params) use($func,$self){
                /** @var $self Lock */
                if ($self->key != '') {
                    $result =  run($self,$params,$func);
                    if (!$result) {
                        return '服务器繁忙';
                    }
                    return  $result;
                }
                return call_user_func($func,...$params);//执行业务逻辑
            };
        };
        return $instance;
    }

];