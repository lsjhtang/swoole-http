<?php
namespace Core\annotations;

use Core\BeanFactory;

return [
    RequestMapping::class=>function(\ReflectionMethod $method,$instance,$self){
        $path = $self->value;
        $request_method = $self->method?: ['GET'];
        $router_collector = BeanFactory::getBean('RouterCollector');

        $router_collector->addRouter($request_method, $path, function () use ($method, $instance) {
            $method->invoke($instance);//执行反射方法
        });
        return $instance;
    },


];