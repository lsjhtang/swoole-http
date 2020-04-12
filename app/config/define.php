<?php

define("ROOT_PATH",dirname(dirname(__DIR__)));

$GLOBAL_CONFIGS = [
    'db'=>require_once (__DIR__.'/database.php'),
    'dbpool'=>require_once (__DIR__.'/dbpool.php'),
    'redis'=>require_once (__DIR__.'/redis.php'),
    'redispool'=>require_once (__DIR__.'/redispool.php'),
];