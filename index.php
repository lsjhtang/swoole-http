<?php
require_once __DIR__."/vendor/autoload.php";
use Swoole\Http\Request;
use Swoole\Http\Response;

require_once __DIR__."/app/config/define.php"; //自定义配置
\Core\BeanFactory::init();//初始化Bean工厂
$dispatcher=\Core\BeanFactory::getBean("RouterCollector")->getDispatcher();



$http = new Swoole\Http\Server("0.0.0.0", 80);
$http->on('request', function (Request $request,Response $response) use($dispatcher) {
    $myrequest=\Core\http\Request::init($request);
    $response=\Core\http\Response::init($response);
    $routeInfo = $dispatcher->dispatch($myrequest->getMethod(),$myrequest->getUri() );
    //[1,$handler,$var]
    switch ($routeInfo[0]) {
        case FastRoute\Dispatcher::NOT_FOUND:
            $response->status(404);
            $response->end();
            break;
        case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
            $response->status(405);
            $response->end();
            break;
        case FastRoute\Dispatcher::FOUND:
            $handler = $routeInfo[1];
            $parameters = $routeInfo[2];
            $ext_params = [$myrequest, $response];
            $response->setBody($handler($parameters, $ext_params));
            $response->end();
            break;
    }

});
$http->start();
