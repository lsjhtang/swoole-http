<?php
namespace Core\init;

use Core\annotations\Bean;
use FastRoute\RouteCollector;
use function FastRoute\simpleDispatcher;

/**
 * 路由收集器
 * @Bean()
 */
class RouterCollector {

    public $routes=[];

    //收集路由
    public function addRouter($method, $uri, $handler)
    {
        $this->routes[] = [
            'method'    => $method,
            'uri'       => $uri,
            'handler'   => $handler,
        ];
    }


    public function getDispatcher()
    {
        return simpleDispatcher(function (RouteCollector $r){
            foreach ($this->routes as $route) {
                $r->addRoute($route['method'], $route['uri'], $route['handler']);
            }
        });
    }
}
