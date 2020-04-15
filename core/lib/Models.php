<?php
namespace Core\lib;

use Core\BeanFactory;
use Core\init\MyDB;
use \Illuminate\Database\Eloquent\Model;

class Models extends Model
{

    public function __call($method, $parameters)
    {
        return $this->invoke(function () use ($method, $parameters){
           return parent::__call($method, $parameters);
        });
    }

    public function save(array $options = [])
    {
        return $this->invoke(function () use ($options){
           return parent::save($options);
        });
    }

    public function update(array $attributes = [], array $options = [])
    {
        return $this->invoke(function () use ($attributes, $options){
            return parent::update($attributes, $options);
        });

    }

    public static function all($columns = ['*'])
    {
        return self::invokeStatic(function () use ($columns){
            return parent::all($columns);
        });
    }

    public function invoke(callable $func)
    {
        $mydb = clone BeanFactory::getBean(MyDB::class);
        $obj = $mydb->model($func);
        try{
            return $obj();
        }catch (\Exception $exception){
            return $exception->getMessage();
        }
        
    }

    public static function invokeStatic(callable $func)
    {
        $mydb = clone BeanFactory::getBean(MyDB::class);
        $obj = $mydb->model($func);
        try{
            return $obj();
        }catch (\Exception $exception){
            return $exception->getMessage();
        }

    }

}