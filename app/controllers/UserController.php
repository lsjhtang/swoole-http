<?php
namespace App\controller;

use App\models\User;
use Core\annotations\Bean;
use Core\annotations\DB;
use Core\annotations\Lock;
use Core\annotations\Redis;
use Core\annotations\RequestMapping;
use Core\annotations\Value;
use Core\http\Request;
use Core\http\Response;
use Core\init\MyDB;
use Core\lib\RedisHelper;
use Swoole\Coroutine;

/**
 * @Bean(name="user")
 */
class UserController{

    /**
     * @DB(source = "default")
     * @var MyDB
     */
    public $db1;

    /**
     * @DB(source = "db2")
     * @var MyDB
     */
    private $db2;

    /**
     * @Value(name="version")
     */
    public $version = '1.0';

    /**
     * @Redis(key = "#820", prefix="test", expire="30" ,type="string", incr="abc")
     * @RequestMapping(value="/test1/{uid:\d+}",method={"GET"})
     */
    public function test1( Request $request, Response $response )
    {
        //$this->db1->setDbSource('default');
        //$users =  User::all();
        $users = $this->db2->table('test')->first();
        /*$users->user_name = 1;
        $users->age = 10;
        $users->save();*/

        //$db = $this->db1->Begin();
        //$this->db1->table('test')->insert(['user_name'=>'zhangshan','age'=>1]);
        //$db->Commit();

        return $users;
    }

    /**
     * @Redis(prefix="stock",key="id",type="sortedset", score="age", member="test", coroutine="true")
     * @RequestMapping(value="/test2/{uid:\d+}",method={"GET"})
     */
    public function test2( Request $request, Response $response)
    {
        $pagesize = 3;
        $chan = new Coroutine\Channel(3);
        for ($i=0;$i<3;$i++){
            go(function () use ($chan,$pagesize,$i){//循环创建协程
                $users = User::take($pagesize)->skip($i*$pagesize)->get()->toArray();
                $chan->push($users);

            });
        }
        return $chan;
    }

    /**
     * @Redis(script="
        return redis.call('ZRANGE','stock','0','-1','withscores')
        ")
     * @RequestMapping(value="/user")
     */
    public function user()
    {
        //return ['name'=>'user','age'=>26];
    }

    /**
     * @Lock(prefix="lock",key="#0")
     * @RequestMapping(value="/lock/{uid:\d+}")
     */
    public function lock($uid)
    {
        $key = 'stock';
        $member = 'pid'.$uid;
        $prodStock = RedisHelper::zScore($key,$member);
        if ($prodStock && $prodStock > 0) {
            return RedisHelper::zIncrBy($key,-1,$member);
        }
        return '卖完了';
    }

}