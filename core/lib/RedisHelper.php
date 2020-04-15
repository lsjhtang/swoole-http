<?php
namespace Core\lib;
use Core\BeanFactory;
use Core\init\PHPRedisPool;

/**
 * Class RedisHelper
 * @method  static string get(string $key)
 * @method  static bool set(string $key,string $value)
 * @method  static bool setex(string $key,int $ttl,string $value)
 * @method  static array hgetall(string $key)
 * @method  static bool hmset(string $key,array $keyandvalues)
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