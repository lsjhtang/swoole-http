<?php
namespace App\test;
use App\annotations\Bean;
use App\annotations\Value;

/**
 * @Bean()
 */
class MyRedis{
    /**
     * @Value(name="url")
     */
    public $conn_url;

}