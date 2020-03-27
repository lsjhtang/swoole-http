<?php
namespace App\controller;

use Core\annotations\Bean;
use Core\annotations\RequestMapping;
use Core\annotations\Value;
use Core\http\Request;
use Core\http\Response;


/**
 * @Bean(name="user")
 */
class UserController{

    /**
     * @Value(name="version")
     */
    public $version = '1.0';

    /**
     * @RequestMapping(value="/test/{uid:\d+}",method={"GET"})
     */
    public function test( Request $request, $uid, Response $response)
    {
        //$response->redirect("www.baidu.com");
        $response->writeHtml('你好');
        return ['name'=>'test','age'=>18];
    }

}