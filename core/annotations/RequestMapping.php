<?php
namespace Core\annotations;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 * @Target({"METHOD"})
 */
class RequestMapping{
    public  $value='';//请求路径  ,如uer/index
    public  $method=[];//请求方式  如get,post

}