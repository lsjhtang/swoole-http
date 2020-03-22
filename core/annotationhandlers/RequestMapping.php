<?php
namespace Core\annotations;

use Core\BeanFactory;

return [
    RequestMapping::class=>function(\ReflectionMethod $method,$instance,$self){
        $path = $self->value;
        $request_method = $self->method?: ['GET'];
        $router_collector = BeanFactory::getBean('RouterCollector');

        $router_collector->addRouter($request_method, $path, function ($parameters, $ext_params) use ($method, $instance) {

            $inputParams = [];
            $ref_params = $method->getParameters();//获得方法的反射参数
            foreach ($ref_params as $ref_param) {
                if (isset($parameters[$ref_param->getName()])) {
                    $inputParams[] = $parameters[$ref_param->getName()];
                }else{
                    foreach ($ext_params as $ext_param) {
                        if ($ref_param->getClass() && $ref_param->getClass()->isInstance($ext_param)){//判断需要的参数类型跟传入的类型是否符合
                            $inputParams[] = $ext_param;
                            continue;
                        }
                    }

                    $inputParams[] = false;
                }
            }

            return  $method->invokeArgs($instance, $inputParams);//执行反射方法
        });
        return $instance;
    },


];