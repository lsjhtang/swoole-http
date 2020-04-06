<?php
namespace Core\annotationhandlers;

use Core\annotations\DB;
use Core\BeanFactory;
use Core\init\MyDB;

return [
    DB::class=>function(\ReflectionProperty $property, $instance, $self){

        if ($self->source != 'default') {
            $bean_name = MyDB::class.'_'.$self->source;
            $mydb_bean = BeanFactory::getBean($bean_name);//从新获取一个对象
            if (!$mydb_bean) {
                $mydb_bean = clone BeanFactory::getBean(MyDB::class);
                $mydb_bean->setDbSource($self->source);//新MyDB对象设置数据源
                BeanFactory::setBean($bean_name, $mydb_bean);//把新的对象塞进容器里面
            }
        } else {
            $mydb_bean = BeanFactory::getBean(MyDB::class);
        }

        $property->setAccessible(true);
        $property->setValue($instance,$mydb_bean);
        return $instance;
    }
];