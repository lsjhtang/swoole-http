<?php
namespace  Core\lib;

abstract class DBPool{
    private $min;
    private $max;
    private $conns;
    private $count;//当前所有连接数
    private $idleTime=40;//连接空闲时间秒
    abstract protected function newDB();
    function __construct($min=10,$max=20,$idleTime=40)
    {
        $this->min=$min;
        $this->max=$max;
        $this->idleTime=$idleTime;
        $this->conns=new \Swoole\Coroutine\Channel($this->max);
        //构造方法直接初始化DB连接
        for($i=0;$i<$this->min;$i++){
            $this->addDBToPool();//统一调用
        }
    }

    public function getCount(){
        return $this->count;
    }

    public function initPool(){ //根据最小连接数，初始化池
//        for($i=0;$i<$this->min;$i++){
//            $this->addDBToPool();//统一调用
//        }
//        \Swoole\Timer::tick(2000,function(){
//           $this-> cleanPool();
//        });
    }

    public function getConnection(){//取出连接
        if($this->conns->isEmpty()){
            if($this->count < $this->max){//连接池没满
                $this->addDBToPool();
                $getObject=$this->conns->pop();
            }else{
                $getObject=$this->conns->pop(5);
            }
        } else {
            $getObject= $this->conns->pop();
        }
        if($getObject)
            $getObject->usedtime=time();//给连接池对象加上时间标签
        return $getObject;
    }

    public function close($conn){//放回连接
        if($conn){
            $this->conns->push($conn);
        }
    }

    public function addDBToPool(){ //把对象加入池
        try{
            $this->count++;
            $db=$this->newDB();
            if(!$db) throw  new \Exception("db创建错误");

            $dbObject=new \stdClass();
            $dbObject->usedTime=time();
            $dbObject->db=$db;
            $this->conns->push($dbObject);
        }catch (\Exception $ex){
            $this->count--;
        }
    }

    private function cleanPool(){//定时清理空闲连接

        if($this->conns->length()<=$this->min && $this->conns->length()<intval($this->max*0.6)){
            return ;
        }
        $dbbak=[];
        while(true){
            if($this->conns->isEmpty()) {
                break;
            }
            $obj=$this->conns->pop(0.1);
            if($this->count> $this->min && (time()-$obj->usedTime)>$this->idleTime){
                $this->count--;
            } else{
                $dbbak[]=$obj;
            }
        }
        foreach ($dbbak as $db){
            $this->conns->push($db);
        }
    }


}