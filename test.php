<?php
require_once __DIR__. '/vendor/autoload.php';
require_once __DIR__.'/app/config/define.php';

\Core\BeanFactory::init();
$user = \Core\BeanFactory::getBean('user');
$users = \Core\BeanFactory::getBean('RouterCollector');
var_dump($user,$users);
exit();