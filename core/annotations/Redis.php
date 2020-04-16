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
    public $expire  = -1;
    public $incr    = '';
    public $member  = '';
    public $score   = '';//有序集合分数
    public $coroutine   = false;//使用协程
}
