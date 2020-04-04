<?php
namespace App\controller;

use Core\annotations\Bean;
use Core\annotations\RequestMapping;
use Core\annotations\Value;
use Core\http\Request;
use Core\http\Response;


/**
 * @Bean(name="abc")
 */
class AbcController{

    /**
     * @Value(name="version")
     */
    public $version = '1.0';

    /**
     * @RequestMapping(value="/abc")
     */
    public function abc( Request $request, $uid, Response $response)
    {
        //$response->redirect("www.baidu.com");
        //$response->writeHtml('你好');
        return ['name'=>'abc','age'=>26];
    }

}