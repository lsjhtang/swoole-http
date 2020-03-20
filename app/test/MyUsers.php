<?php
namespace App\test;
use App\annotations\Value;
use App\annotations\Bean;

/**
 * @Bean()
 */
class MyUsers{
    /**
     * @Value(name="url")
     */
    public $conn_url =1;

}