<?php
namespace App\annotations;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 * @Target({"PROPERTY"})
 */
class Value{

    public $name;

    public function readFile()
    {
       $file_ini =  parse_ini_file('env');
       if (isset($file_ini[$this->name]))  return $file_ini[$this->name];
       return false;

    }

}