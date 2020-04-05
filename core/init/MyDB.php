<?php

namespace Core\init;

use Core\annotations\Bean;
use Illuminate\Database\Capsule\Manager as lvDB;


/**
 *
 * @method  \Illuminate\Database\Query\Builder table(string $table, string|null  $as=null, string|null  $connection=null)
 */
class MyDB
{
    private $lvDB;

    public function __construct()
    {
        global $GLOBALS_CONFIGS;
        if (isset($GLOBALS_CONFIGS['db']) && isset($GLOBALS_CONFIGS['db']['default'])) {

            $this->lvDB = new lvDB();
            $this->lvDB->addConnection($GLOBALS_CONFIGS['db']['default']);
            $this->lvDB->setAsGlobal();
            $this->lvDB->bootEloquent();
        }
    }

    public function __call($name, $arguments)
    {
       return  $this->lvDB::$name(...$arguments);
    }


}