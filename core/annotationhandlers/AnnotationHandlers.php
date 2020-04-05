<?php
namespace Core\annotationhandlers;

use Core\annotations\Value;
use Core\annotations\Bean;

return [
    Bean::class=>function($instance,$container,$self){

        $vars = get_object_vars($self);//获取类的属性

        if (isset($vars['name']) && !empty($vars['name'])){
            $beanName = $vars['name'];
        }else{
            $beanName =  end(explode('\\',get_class($instance)));
        }
        $container->set($beanName,$instance);
    },

    Value::class=>function(\ReflectionProperty $property, $instance, $self){
        $env = parse_ini_file(ROOT_PATH.'/env');
        if (!isset($env[$self->name]) || empty($self->name)) return $instance;
        $property->setValue($instance,$env[$self->name]);
        return $instance;
    }
];