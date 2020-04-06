<?php

namespace App\models;

use Core\lib\Model;

class User extends Model
{
    protected $table = 'test';
    protected $primaryKey = 'id';
    protected $connection = 'default';
}