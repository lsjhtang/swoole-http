<?php
namespace App\controller;

use App\models\User;
use Core\annotations\Bean;
use Core\annotations\DB;
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
     * @RequestMapping(value="/test1/{uid:\d+}",method={"GET"})
     */
    public function test1( Request $request, $uid, Response $response)
    {
        //$this->db1->setDbSource('default');
        //return User::find(1);
        $this->db1->Begin();
        $this->db1->table('test')->insert(['user_name'=>'zhangshan','age'=>1]);
        $this->db1->Rollback();

        return 1;
    }

    /**
     * @RequestMapping(value="/test2/{uid:\d+}",method={"GET"})
     */
    public function test2( Request $request, $uid, Response $response)
    {
        //$this->db2->setDbSource('db2');
        return $this->db1->select('select sleep(8)');
    }

    /**
     * @RequestMapping(value="/user")
     */
    public function user()
    {
        return ['name'=>'user','age'=>26];
    }

}