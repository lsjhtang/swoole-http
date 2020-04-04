<?php
namespace Core\server;

use Core\init\TestProcess;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\Http\Server;

class HttpServer{
    private $server;
    public function __construct()
    {
        $this->server = new \Swoole\Http\Server("0.0.0.0", 80);
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
        require_once (__DIR__.'./../../test.php');
        cli_set_process_title('buddha worker');
    }

    public function onRequest(Request $request, Response $response){
        $response->end(test());
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
        $p = new TestProcess();
        $this->server->addProcess($p->run());//监控

        $this->server->start();
    }


}