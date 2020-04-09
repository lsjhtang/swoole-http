<?php

namespace App\models;

use Core\lib\Models;

class User extends Models
{
    protected $table = 'test';
    protected $primaryKey = 'id';
    protected $connection = 'default';
}