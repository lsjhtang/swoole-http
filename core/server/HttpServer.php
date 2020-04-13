<?php
namespace Core\server;

use Core\init\TestProcess;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\Http\Server;

class HttpServer{
    private $server;
    private $dispatcher;

    public function __construct()
    {
        $this->server = new Server("0.0.0.0", 80);
        $this->server ->set(array(
            'worker_num' => 1,
            'daemonize' => false,
        ));
        $this->server ->on('request', [$this,"onRequest"]);
        $this->server ->on('Start', [$this,"onStart"]);
        $this->server ->on('ShutDown', [$this,"onShutDown"]);
        $this->server ->on('WorkerStart', [$this,"onWorkerStart"]);
        $this->server ->on('ManagerStart', [$this,"onManagerStart"]);
    }

    public function onWorkerStart(Server $server, int $workerId)
    {
        cli_set_process_title('buddha worker');
        \Core\BeanFactory::init();//初始化Bean工厂
        $this->dispatcher=\Core\BeanFactory::getBean("RouterCollector")->getDispatcher();
    }

    public function onRequest(Request $request, Response $response){
        $myrequest=\Core\http\Request::init($request);
        $myresponse=\Core\http\Response::init($response);
        $routeInfo = $this->dispatcher->dispatch($myrequest->getMethod(),$myrequest->getUri() );
        //[1,$handler,$var]
        switch ($routeInfo[0]) {
            case \FastRoute\Dispatcher::NOT_FOUND:
                $response->status(404);
                $response->end();
                break;
            case \FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                $response->status(405);
                $response->end();
                break;
            case \FastRoute\Dispatcher::FOUND:
                $handler = $routeInfo[1];
                $parameters = $routeInfo[2];
                $ext_params = [$myrequest, $myresponse];
                $myresponse->setBody($handler($parameters, $ext_params));
                $myresponse->end();
                break;
        }
    }

    public function onManagerStart(Server $server)
    {
        cli_set_process_title('buddha manger');

    }

    public function onStart(Server $server){
        cli_set_process_title('buddha master');
        $mid= $server->master_pid;
        file_put_contents("./Buddha.pid",$mid);
    }
    public function onShutDown(Server $server){
        unlink("./Buddha.pid");
    }

    public function run(){
        $developer = parse_ini_file(ROOT_PATH.'/.env');
        if ($developer['developer']) {
            $p = new TestProcess();
            $this->server->addProcess($p->run());//监控 开发者模式
        }

        $this->server->start();
    }


}