<?php

namespace App\Models;

use App\Database\Database;
use App\Database\DatabaseConnection;
use PDO;

class BaseModel {
    public static function getDB() :Database
    {
        return new Database();
    }

    public static function getDBConnection() :PDO
    {
        return (new DatabaseConnection())->getPDO();
    }
}