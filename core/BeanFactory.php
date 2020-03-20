<?php
namespace Core;

use DI\ContainerBuilder;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;

class BeanFactory{
    private static $env=[];//配置文件
    private static $container;//ioc容器
    private static $handler=[];//

    public static function init()
    {
        self::$env=parse_ini_file(ROOT_PATH.'/env');
        $builder =  new ContainerBuilder();//初始化builder
        $builder->useAnnotations(true);//启用注解
        self::$container = $builder->build();//初始化容器
        $handlers = glob(ROOT_PATH.'/core/annotationhandlers/*.php');
        foreach ($handlers as $handler) {
            self::$handler = array_merge(self::$handler,require_once($handler));
        }
        $loader = require __DIR__.'/../vendor/autoload.php';
        AnnotationRegistry::registerLoader([$loader,'loadClass']);//设置注解加载
        self::ScanBeans();//扫描指定文件夹下所有文件
    }

    public static function getEnv(string $key, string $default='')//读取配置文件内容
    {
        if (isset(self::$env[$key])) return self::$env[$key];
        return $default;
    }

    public static function getBean($name)
    {
        return self::$container->get($name);
    }

    public static function ScanBeans()
    {
        $scan_dir = self::getEnv('scan_dir',ROOT_PATH.'/app');
        $scan_root_namespace = self::getEnv('scan_root_namespace','App\\');
        $files = glob($scan_dir.'/*.php');//读取文件夹下面所有.php文件
        foreach ($files as $file) {
            require_once ($file);
        }

        $reader = new AnnotationReader();
        $clsses = get_declared_classes();//获取所有注册过的类
        foreach ($clsses as $class) {
            if (strstr($class,$scan_root_namespace)){
                $ref_class = new \ReflectionClass($class);
                $class_annos = $reader ->getClassAnnotations($ref_class);//获取类上面的所有注解

                //处理类注解
                foreach ($class_annos as $class_anno) {
                   $handler = self::$handler[get_class($class_anno)];//获取处理过程
                    $instance =  self::$container->get($ref_class->getName());
                    //处理属性注解
                    self::handlerProAnno($instance, $ref_class, $reader);
                    //处理方法注解
                    self::handlerMethodAnno($instance, $ref_class, $reader);

                   $handler($instance,self::$container,$class_anno);//执行处理过程,usercontroller类的全名称

                }
            }
        }
    }

    //处理属性注解
    public static function handlerProAnno(&$instance,\ReflectionClass $ref_class, AnnotationReader $reader)
    {
        $properties =  $ref_class->getProperties();//反射获取所有属性

        foreach ($properties as $property) {
            $pro_anno = $reader->getPropertyAnnotations($property);//获取所有属性的的注解
            foreach ($pro_anno as $anno) {
                $handler = self::$handler[get_class($anno)];

                $handler($property, $instance, $anno);//对属性赋值 反射属性对象,容器里面的类对象.注解对象
            }
        }
    }

    //处理方法注解
    public static function handlerMethodAnno(&$instance,\ReflectionClass $ref_class, AnnotationReader $reader)
    {
        $methods =  $ref_class->getMethods();//反射获取所有方法

        foreach ($methods as $method) {
            $methods_anno = $reader->getMethodAnnotations($method);//获取所有方法的的注解
            foreach ($methods_anno as $method_anno) {
                $handler = self::$handler[get_class($method_anno)];

                $handler($method, $instance, $method_anno);//反射方法对象,容器里面的类对象.注解对象
            }
        }
    }

}