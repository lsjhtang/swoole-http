<?php

namespace Core\init;
use Swoole\Process;

class TestProcess
{
    private $md5file;

    public function run()
    {
        return new Process(function(){
            while (true){
             sleep(3);
             $files = glob(__DIR__.'/../../*.php');
             $md5_value = '';
             foreach ($files as $item) {
                 $md5_value .= md5_file($item);
             }

            if ($this->md5file == '') {
                $this->md5file = $md5_value;
                continue;
            }

            if (strcmp($this->md5file,$md5_value) !== 0) {//代表文件被改动
                $this->md5file = $md5_value;
                $get_pid=intval(file_get_contents("./Buddha.pid")); //获取上一次程序运行的 master_id

                Process::kill($get_pid,SIGUSR1);

            }

            }
        });

    }

}