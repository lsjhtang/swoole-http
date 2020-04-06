<?php
namespace App\pool;

abstract class DBPool
{
   private $min;
   private $max;
   private $connects;
   abstract protected function newDB();

   public function __construct($min = 5, $max = 10)
   {
       $this->min = $min;
       $this->max = $max;
       $this->connects = new \Swoole\Coroutine\Channel($max);

   }

    public function initPool()
    {
       for ($i=0; $i<$this->max; $i++){
           $db = $this->newDB();
           $this->connects->push($db);
       }
    }

    public function getConnection()//取出对象
    {
        return $this->connects->pop();
    }

    public function close($con)//放回连接
    {
        $this->connects->push($con);

    }
}