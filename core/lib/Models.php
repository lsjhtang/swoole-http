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

    public function invoke(callable $func)
    {
        $mydb = clone BeanFactory::getBean(MyDB::class);
        $obj = $mydb->genConnection();
        try{
            return $func();
        }catch (\Exception $exception){
            return $exception->getMessage();
        }

        finally{
            $mydb->releaseConnection($obj);
        }

    }

}