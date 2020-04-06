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
        $builder->useAnnotations(true);//启用容器注解
        self::$container = $builder->build();//初始化容器
        $handlers = glob(ROOT_PATH.'/core/annotationhandlers/*.php');
        foreach ($handlers as $handler) {
            self::$handler = array_merge(self::$handler,require($handler));
        }
        $loader = require __DIR__.'/../vendor/autoload.php';
        AnnotationRegistry::registerLoader([$loader,'loadClass']);//设置注解加载

        $scans = [
            ROOT_PATH.'/core/init'=>"Core\\",
            self::getEnv('scan_dir',ROOT_PATH.'/app')=>self::getEnv('scan_root_namespace','App\\'),
        ];
        foreach ($scans as $scan_dir=>$scan_root_namespace) {

            self::ScanBeans($scan_dir, $scan_root_namespace);//扫描指定文件夹下所有文件
        }

    }

    //读取配置文件
    public static function getEnv(string $key, string $default='')//读取配置文件内容
    {
        if (isset(self::$env[$key])) return self::$env[$key];
        return $default;
    }

    //从容器获取对象
    public static function getBean($name)
    {
        try {
            return self::$container->get($name);
        } catch (\Exception $exception) {
            return false;
        }
    }

    //把对象设置到容器里面 key=>value
    public static function setBean($name,$value)
    {
        self::$container->set($name,$value);
    }

    public static function getAllBeanFiles($dir)//递归扫描文件
    {
        $ret = [];
        $files = glob($dir.'/*');
        foreach ($files as $file) {
            if (is_dir($file) ) {
               $ret = array_merge($ret, self::getAllBeanFiles($file));//递归读取所有文件夹下面的内容
            } elseif(pathinfo($file)['extension'] == 'php') {
                $ret[] = $file;
            }
        }

        return $ret;
    }
    
    public static function ScanBeans($scan_dir, $scan_root_namespace)
    {
        $all_files = self::getAllBeanFiles($scan_dir);//读取文件夹下面所有.php文件
        foreach ($all_files as $file) {
            require_once ($file);
        }

        $reader = new AnnotationReader();
        $classes = get_declared_classes();//获取所有注册过的类
        foreach ($classes as $class) {
            if (strstr($class,$scan_root_namespace) && !strstr($class,$scan_root_namespace."annotations") ){
                $ref_class = new \ReflectionClass($class);
                $class_annos = $reader ->getClassAnnotations($ref_class);//获取类上面的所有注解

                //处理类注解
                foreach ($class_annos as $class_anno) {
                    if(!isset(self::$handler[get_class($class_anno)])) continue;//如果没有做注解处理函数不作处理
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
                if(!isset(self::$handler[get_class($anno)])) continue;//没有注解的属性不处理
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
                if(!isset(self::$handler[get_class($method_anno)])) continue;//没有注解的方法不处理
                $handler = self::$handler[get_class($method_anno)];

                $handler($method, $instance, $method_anno);//反射方法对象,容器里面的类对象.注解对象
            }
        }
    }

}