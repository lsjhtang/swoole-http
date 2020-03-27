<?php
namespace Core\server;
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
    }

    public function onRequest(){
    }

    public function onStart(Server $server){
        $mid= $server->master_pid;
        file_put_contents("./Buddha.pid",$mid);
    }
    public function onShutDown(Server $server){
        unlink("./Buddha.pid");
    }
    public function run(){
        $this->server->start();
    }


}