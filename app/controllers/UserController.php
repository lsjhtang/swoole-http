<?php
namespace App\controller;

use App\models\User;
use Core\annotations\Bean;
use Core\annotations\DB;
use Core\annotations\Redis;
use Core\annotations\RequestMapping;
use Core\annotations\Value;
use Core\http\Request;
use Core\http\Response;
use Core\init\MyDB;

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
     * @Redis(key = "#1", prefix="test")
     * @RequestMapping(value="/test1/{uid:\d+}",method={"GET"})
     */
    public function test1( Request $request, $uid, Response $response )
    {
        //$this->db1->setDbSource('default');
        //$users =  User::all();
        $users = $this->db2->table('test')->get();
        /*$users->user_name = 1;
        $users->age = 10;
        $users->save();*/

        //$db = $this->db1->Begin();
        //$this->db1->table('test')->insert(['user_name'=>'zhangshan','age'=>1]);
        //$db->Commit();

        return $users;
    }

    /**
     * @RequestMapping(value="/test2/{uid:\d+}",method={"GET"})
     */
    public function test2( Request $request, Response $response)
    {
        $users = new User();
        $users->user_name = 1;
        $users->age = 10;
        $users->save();
        return 2;
        //$this->db2->setDbSource('db2');
        //return $this->db1->select('select sleep(8)');
    }

    /**
     * @RequestMapping(value="/user")
     */
    public function user()
    {
        return ['name'=>'user','age'=>26];
    }

}