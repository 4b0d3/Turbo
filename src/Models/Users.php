<?php 

namespace App\Models;

use App\Database\Database;
use App\Models\Roles;
use App\Entity\FormChecker;

class Users {
    public static function get(int $id)
    {
        if($id == null && $id <= 0) return null;

        $db = new Database();
        $q = "SELECT users.*, roles.name as role FROM users LEFT JOIN roles ON users.role = roles.id WHERE users.id = ?";

        $res = $db->queryOne($q, [$id]) ?: null;
        return $res;
    }

    public static function getByMail(string $email)
    {
        $db = new Database();
        $q = "SELECT users.*, roles.name as role FROM users LEFT JOIN roles ON users.role = roles.id WHERE users.email = ?";

        $res = $db->queryOne($q, [$email]) ?: null;
        return $res;
    }

    public static function getAll(int $start = null, int $total = null)
    {
        $db = new Database();
        $q = "SELECT users.*, roles.name as role FROM users LEFT JOIN roles ON users.role = roles.id";

        $res = null;
        if(!($start == null || $start < 0 || $total == null || $total < 0 )) {
            $q = $q . " LIMIT " . $start . ", " . $total; 
            $res = $db->queryAll($q);
        }

        $res = $db->queryAll($q) ?: null;

        return $res;
    }

    public static function add(array $userInfo) :array
    {
        $data = [];
        $fields = [
            [ "type" => "email", "name" => "email" ],
            [ "type" => "name", "name" => "name" ],
            [ "type" => "firstName", "name" => "firstName" ],
            [ "type" => "password", "name" => "password" ],
            [ "type" => "password", "name" => "confirmPassword" ],
            [ "type" => "role", "name" => "role" ],
        ];

        $data = (new FormChecker)->check($fields, $userInfo);

        if(!$data["status"]) {
            return $data;
        }

        $user = $data["user"];
        $alreadyExists = Users::getByMail($user["email"]);

        if($alreadyExists != null) {
            $data["status"] = false;
            $data["boxMsgs"] = [["status" => "Erreur", "class" => "error", "description" => "L'utilisateur n'a pas été créé : l'adresse mail est déjà utilisée."]];
            return $data;
        }        
        
        if(!isset($user["roleId"])) {
            $user["roleId"] = Roles::getId("user");
        }

        $db = new Database();
        $q = "INSERT INTO users(email, password, name, firstName, role) VALUES(:email, :password, :name, :firstName, :roleId)";

        $user["password"] = password_hash($user["password"], PASSWORD_DEFAULT); // TODO CHANGE PASSWORD_DEFAULT

        $res = $db->query($q, $user);

        if(!$res) {
            $data["status"] = false;
            $data["boxMsgs"] = [["status" => "Erreur", "class" => "error", "description" => "L'utilisateur n'a pas été créé : problème lors de la requête d'ajout de l'utilisateur dans la base de données."]];
            return $data;
        }

        $data["boxMsgs"] = [["status" => "Succès", "class" => "success", "description" => "L'utilisateur a bien été créé."]];
        return $data;
    }

    public static function updateOneById(array $user) :bool
    {
        $data = [];
        $fields = [
            [ "type" => "int", "name" => "id" ],
            [ "type" => "email", "name" => "email" ],
            [ "type" => "name", "name" => "name" ],
            [ "type" => "firstName", "name" => "firstName" ],
            [ "type" => "password", "name" => "password" ],
            [ "type" => "password", "name" => "confirmPassword" ],
            [ "type" => "role", "name" => "role" ],
            [ "type" => "checkbox", "name" => "confirmed" ]
        ];

        $data = (new FormChecker)->check($fields, $user);

        if(!$data["status"]) {
            return $data;
        }

        $set = [];
        $allowedKeys = ["email", "password", "name", "firstName", "role", "confirmed"];

        foreach ($user as $key => $value) {
            if (!in_array($key, $allowedKeys)) {
                continue;
            }

            $set[] = "$key = :$key";
        }

        $set = implode(", ", $set);
        $db = new Database();
        $res = $db->query("UPDATE users SET $set WHERE id = :id",  $user);

        return $res;
    }

    public static function delete(int $id) :bool
    {
        if($id == null && $id <= 0) return false;

        $db = new Database();
        $q = "DELETE FROM users WHERE id = ?";

        $res = $db->query($q, [$id]);

        return $res;
    }
}