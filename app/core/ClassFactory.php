<?php
namespace App\core;

use App\annotations\Bean;
use Doctrine\Common\Annotations\AnnotationReader;

class ClassFactory {

    private static $Beans;

    public static function getBean($classname)
    {
        if (isset(self::$Beans[$classname])) {
            return self::$Beans[$classname];
        }
        return false;
    }

    public static function ScanBeans(String  $path, String $namespace)
    {
        $files = glob($path.'/*.php');//读取文件夹下面所有.php文件
        foreach ($files as $file) {
            require_once ($file);
        }

        $reader = new AnnotationReader();
        $clsses = get_declared_classes();//获取所有注册过的类
        foreach ($clsses as $clss) {
            if( strstr($clss, $namespace) ){
                $ref_class = new \ReflectionClass($clss);
                $annos = $reader ->getClassAnnotations($ref_class);//获取类上面的所有注解
                foreach ($annos as $anno) {
                    if ($anno instanceof Bean) {
                        self::$Beans[$ref_class->getName()] = self::loadClass($ref_class->getName(),$ref_class->newInstance());
                    }
               }
             }
        }

    }


    public static function loadClass($classname,$object=false)
    {
        $ref_class = new \ReflectionClass($classname);
        $properties =  $ref_class->getProperties();//反射获取所有属性

        $reader = new AnnotationReader();
        foreach ($properties as $property) {
            $anno = $reader->getPropertyAnnotations($property);//获取所有属性的的注解
            foreach ($anno as $item) {
                $class = $object ?: $ref_class->newInstance();

                $property->setValue($class,$item->readFile());
                return $class;
            }
        }
        return  $object ?: $ref_class->newInstance();
    }
}