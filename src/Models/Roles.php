<?php 

namespace App\Models;

use App\Database\Database;

class Roles {
    public static function getId(string $role) 
    {
        $db = new Database();
        $role = $db->queryOne("SELECT id FROM roles WHERE name = ?", [$role]);

        if($role != false) {
            return $role["id"];
        }

        return null;
    }

    public static function getAll()
    {
        $db = new Database();
        $roles = $db->queryAll("SELECT * FROM roles order by id DESC");

        if($roles != false) {
            return $roles;
        }

        return null;
    }
}