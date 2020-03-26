<?php

use Swoole\Http\Server;

if ($argc == 2) {
    $cmd = $argv[1];

    if ($cmd == 'start') {
        startSwoole();
    } elseif ($cmd == 'stop') {
        killSwoole();
    }elseif ($cmd == 'restart'){
        $re =   killSwoole();
        echo $re.PHP_EOL;
        if($re){
            sleep(1);
            echo '启动成功'.PHP_EOL;
            startSwoole();
        }else{
            echo '重启失败!'.PHP_EOL;
        }
    }
}

function startSwoole () {
    $http = new Swoole\Http\Server('0.0.0.',80);
    $http->set([
        'worker_num'=>1,
        'daemonize'=>false,
    ]);
    $http->on('request',function($req,$res){

    });
    $http->on('Start',function(Server $server){
        $mid = $server->master_pid;
        file_put_contents('./top.pid',$mid);
    });
    $http->start();
}

function killSwoole(){
    $mid = intval(file_get_contents('./top.pid'));
    if ($mid && trim($mid) !=0){
       $re =  \Swoole\Process::kill($mid);
       return $re;
    }else{
        return false;
    }
}