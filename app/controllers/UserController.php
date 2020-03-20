<?php
namespace App\controller;

use Core\annotations\Bean;
use Core\annotations\Value;

/**
 * @Bean(name="user")
 */
class UserController{

    /**
     * @Value(name="version")
     */
    public $version = '1.0';
}