<?php 

namespace App\Database;

use \PDO;
use \Exception;

class DatabaseConnection {

    public function __construct()
    {

    }

    public static function getPDO(string  $host = null, string $dbname = null, string $user = null, string $password = null, array $options = null) :PDO
    {
        $host = $host == null ? $_ENV["DB_HOST"] : $host;
        $dbname = $dbname == null ? $_ENV["DB_NAME"] : $dbname;
        $user = $user == null ? $_ENV["DB_USER"] : $user;
        $password = $password == null ? $_ENV["DB_PASSWORD"] : $password;
        $options = $options == null ? array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC) : $options;

        try 
        {
            return new PDO("mysql:host=$host; dbname=$dbname; charset=utf8", $user, $password, $options);
        }
        catch (Exception $e)
        {
            die('Erreur : ' . $e->getMessage());
        }
    }

}