<?php

namespace App\pool;


class CoMysqlPool extends DBPool
{

    public function __construct(int $min = 5, int $max = 10)
    {
        parent::__construct($min, $max);
    }

    protected function newDB()
    {
        $swoole_mysql = new Swoole\Coroutine\MySQL();
        $swoole_mysql->connect([
            'host'     => '127.0.0.1',
            'port'     => 3306,
            'user'     => 'user',
            'password' => 'pass',
            'database' => 'test',
        ]);

        return $swoole_mysql;
    }
}