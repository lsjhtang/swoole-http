<?php

namespace Core\init;

use Core\helper\FileHelper;
use Swoole\Process;

class TestProcess
{
    private $md5file;

    public function run()
    {
        return new Process(function(){
            while (true){
             sleep(3);
             $md5_value = FileHelper::getFileMd5(ROOT_PATH.'/app/*','/app/config');
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