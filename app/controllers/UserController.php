<?php
namespace App\controller;

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
     * @DB()
     * @var MyDB
     */
    public $db;

    /**
     * @Value(name="version")
     */
    public $version = '1.0';

    /**
     * @RequestMapping(value="/test/{uid:\d+}",method={"GET"})
     */
    public function test( Request $request, $uid, Response $response)
    {
        return $this->db->table('test')->get();
    }

    /**
     * @RequestMapping(value="/user")
     */
    public function user()
    {
        return ['name'=>'user','age'=>26];
    }

}