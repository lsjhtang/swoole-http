<?php

namespace Core\init;

use Core\annotations\Bean;
use DI\Annotation\Inject;
use Illuminate\Database\Capsule\Manager as lvDB;


/**
 *  @Bean
 * @method  \Illuminate\Database\Query\Builder table(string $table, string|null  $as=null, string|null  $connection=null)
 */
class MyDB{

    private $lvDB;
    private $dbSource = 'default';
    private $transctionDB = false;

    /**
     * @Inject()
     * @var PDOPool
     */
    public $pdopool;

    public function __construct()
    {
        global $GLOBAL_CONFIGS;
        //default 为默认数据源
        if(isset($GLOBAL_CONFIGS['db'])){
            $configs=$GLOBAL_CONFIGS['db'];
            $this->lvDB=new lvdb();
            foreach ($configs as $key=>$value)
            {
                //  $this->lvDB->addConnection($value,$key);
                $this->lvDB->addConnection(["driver"=>"mysql"],$key);
            }

            $this->lvDB->setAsGlobal();
            $this->lvDB->bootEloquent();
        }
    }
    public function __call($methodName, $arguments)
    {
        if ($this->transctionDB) {//事务对象
            $pdo_object = $this->transctionDB;
            $isTranstion = true;
        }else{
            $isTranstion = false;
            $pdo_object = $this->pdopool->getConnection();
        }
        try{
            if(!$pdo_object) {
                return [];
            }
            $this->lvDB->getConnection($this->dbSource)->setPdo($pdo_object->db);//设置pdo对象
            if ($isTranstion) {//开启事务
                $aa = $this->lvDB->getConnection($this->dbSource)->beginTransaction();
                var_dump($aa);
            }
            $ret=$this->lvDB::connection($this->dbSource)->$methodName(...$arguments);
            return $ret;
        }catch (\Exception $exception){
            return null;
        }

        finally{
            if($pdo_object && ! $isTranstion){
                $this->pdopool->close($pdo_object); //放回连接
            }
        }

        //return $this->lvDB::Connection($this->dbSource)->$methodName(...$arguments);
    }

    /**
     * @return string
     */
    public function getDbSource(): string
    {
        return $this->dbSource;
    }

    /**
     * @param string $dbSource
     */
    public function setDbSource(string $dbSource): void
    {
        $this->dbSource = $dbSource;
    }

    /**
     * 开启事务
     */
    public function Begin()
    {
        $this->transctionDB = $this->pdopool->getConnection();
    }

    /**
     * 提交事务
     */
    public function Commit()
    {
        try{
            $this->lvDB->getConnection($this->dbSource)->commit();
        }
        finally{
            if ($this->transctionDB) {
                $this->pdopool->close($this->transctionDB);
                $this->transctionDB = false;
            }
        }
    }


    /**
     * 事务回滚
     */
    public function RollBack()
    {
        try{
            $this->lvDB->getConnection($this->dbSource)->rollBack();
        }catch (\Exception $exception) {
            return $exception->getMessage();
        }
        finally{
            if ($this->transctionDB) {
                $this->pdopool->close($this->transctionDB);
                $this->transctionDB = false;
            }
        }
    }

}