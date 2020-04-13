<?php
namespace Core\lib;
use Core\BeanFactory;
use Core\init\PHPRedisPool;

/**
 * Class RedisHelper
 * @method  static string get(string $key)
 * @method  static string set(string $key, string $value)
 */
class RedisHelper{


    public static function __callStatic($name, $arguments)
    {
        /** @var  $pool PHPRedisPool */
        $pool=BeanFactory::getBean(PHPRedisPool::class);
        $redis_obj=$pool->getConnection();
        try{
            if(!$redis_obj) {
                return false;
            }

            return $redis_obj->redis->$name(...$arguments);
        }catch (\Exception $exception){
            return $exception->getMessage();
        }finally{
            if($redis_obj)
                $pool->close($redis_obj);
        }
    }
}