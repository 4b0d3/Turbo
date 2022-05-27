<?php 

namespace App\Models;

use App\Database\Database;
use App\Entity\FormChecker;
use App\Entity\User;

class Roles {
    public static function get(int $id)
    {
        if($id == null && $id <= 0) return null;

        $db = new Database();
        $q = "SELECT * FROM roles WHERE id = ?";

        $res = $db->queryOne($q, [$id]) ?: null;
        return $res;
    }

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
        $roles = $db->queryAll("SELECT * FROM roles order by id");

        if($roles != false) {
            return $roles;
        }

        return null;
    }

    public static function add($roleInfo)
    {
        $data = [];
        $fields = [
            [ "type" => "rolename", "name" => "name" ],
        ];

        $data = (new FormChecker)->check($fields, $roleInfo, "Le rôle n'a pas été crée");

        if(!$data["status"]) {
            return $data;
        }

        $role = $data["form"]["checkedFields"];
        $alreadyExists = Roles::getId($role["name"]);

        if($alreadyExists != null) {
            $data["status"] = false;
            $data["boxMsgs"] = [["status" => "Erreur", "class" => "error", "description" => "Le rôle n'a pas été créé : le nom est déjà utilisé."]];
            return $data;
        }        

        $db = new Database();
        $currentUser = new User();

        $q = "INSERT INTO roles(name, createdBy) VALUES(?,?)";
        $res = $db->query($q, [$role["name"], $currentUser->get("id")]);

        if(!$res) {
            $data["status"] = false;
            $data["boxMsgs"] = [["status" => "Erreur", "class" => "error", "description" => "Le rôle n'a pas été créé : problème lors de la requête d'ajout du rôle dans la base de données."]];
            return $data;
        }

        $data["boxMsgs"] = [["status" => "Succès", "class" => "success", "description" => "Le rôle a bien été créé."]];
        return $data;  
    }

    public static function delete(int $id) :bool
    {
        if($id == null && $id <= 0) return false;

        $db = new Database();
        $q = "DELETE FROM roles WHERE id = ?";

        $res = $db->query($q, [$id]);

        $q = "UPDATE users SET role = ? WHERE role = ?";
        $db->query($q, [Roles::getId("user"), $id]);

        return $res;
    }
}