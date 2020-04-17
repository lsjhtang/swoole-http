<?php
namespace Core\lib;
use Core\BeanFactory;
use Core\init\PHPRedisPool;
use phpDocumentor\Reflection\Types\Array_;

/**
 * Class RedisHelper
 * @method  static string get(string $key)
 * @method  static bool set(string $key,string $value)
 * @method  static bool setex(string $key,int $ttl,string $value)
 * @method  static array hgetall(string $key)
 * @method  static bool hmset(string $key,array $keyandvalues)
 * @method  static bool zAdd(string $key, int $score, string $member)
 * @method  static mixed eval($script, $args=array(),$numberKeys=0)
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