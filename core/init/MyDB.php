<?php

namespace Core\init;

use Core\annotations\Bean;
use Illuminate\Database\Capsule\Manager as lvDB;


/**
 *  @Bean
 * @method  \Illuminate\Database\Query\Builder table(string $table, string|null  $as=null, string|null  $connection=null)
 */
class MyDB{

    private $lvDB;
    private $dbSource = 'default';

    public function __construct()
    {
        global $GLOBAL_CONFIGS;
        //default 为默认数据源
        if(isset($GLOBAL_CONFIGS['db'])){
            $config_db = $GLOBAL_CONFIGS['db'];
            $this->lvDB=new lvdb();
            foreach ($config_db as $key => $value) {
                $this->lvDB->addConnection($value, $key);
            }
            $this->lvDB->setAsGlobal();
            $this->lvDB->bootEloquent();
        }
    }
    public function __call($methodName, $arguments)
    {
        // $this->lvDB::table()
        return $this->lvDB::Connection($this->dbSource)->$methodName(...$arguments);
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

}