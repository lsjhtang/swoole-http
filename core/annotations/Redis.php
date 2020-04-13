<?php
namespace Core\annotations;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 * @Target({"METHOD"})
 */
class Redis{
    public $source  = 'default';
    public $key     = '';
    public $type    = 'string';
    public $prefix  = '';
}
