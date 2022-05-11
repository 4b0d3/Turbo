<?php 

namespace App\Models;

use App\Database\Database;
use App\Models\Roles;

class Users {
    public static function get(int $id)
    {
        $db = new Database();
        $q = "SELECT users.*, roles.name as role FROM users LEFT JOIN roles ON users.role = roles.id WHERE users.id = ?";

        $res = [];
        if($id != null && $id > 0) {
            $res = $db->queryOne($q, [$id]);
        }

        return $res;
    }

    public static function getAll(int $start = null, int $total = null) :array
    {
        $db = new Database();
        $q = "SELECT * FROM users";

        $res = [];
        if(!($start == null || $start < 0 || $total == null || $total < 0 )) {
            $q = $q . " LIMIT " . $start . ", " . $total; 
            $res = $db->queryAll($q);
        } else {
            $res = $db->queryAll($q);
        }

        return $res;
    }

    public static function add(array $user) :bool
    {
        if(!isset($user["email"]) || !isset($user["password"]) || !isset($user["firstName"]) || !isset($user["name"])) {
            return false;
        }
        
        $db = new Database();
        $q = "INSERT INTO users(email, password, name, firstName, role) VALUES(:email, :password, :name, :firstName, :roleId)";

        if(!isset($user["roleId"])) {
            $user["roleId"] = Roles::getId("user");
        }

        $user["password"] = password_hash($user["password"], PASSWORD_DEFAULT);

        $res = $db->query($q, $user);

        return $res;
    }

    public static function updateOneById(int $id, array $user) :bool
    {
        $set = [];
        $allowedKeys = ["name", "firstName", "email", "password", "role"];

        foreach ($user as $key => $value) {
            if (!in_array($key, $allowedKeys)) {
                continue;
            }

            $set[] = "$key = :$key";
        }

        $set = implode(", ", $set);
        $db = new Database();
        $res = $db->query("UPDATE users SET $set WHERE id = :id", array_merge(["id" => $id], $user));

        return $res;
    }

    public static function delete(int $id) :bool
    {
        $db = new Database();
        $q = "DELETE FROM users WHERE id = ?";

        $res = false;
        if($id != null && $id > 0) {
            $res = $db->query($q, [$id]);
        }

        return $res;
    }
}