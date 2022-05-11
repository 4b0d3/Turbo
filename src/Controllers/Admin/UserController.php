<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\Users;
use App\Database\Database;

class UserController extends BaseController 
{
    public function get()
    {
            $data["users"] = Users::getAll();
            $data["header"]["admin"] = 1;
            
            $this->display("Admin/updateUsers.html.twig", $data);
    }

    public function post(){

        $data = [];

        if(!isset($_POST["email"]) || empty($_POST["email"])) {
            $data["error"]["message"] = "Veuillez renseignez la geo";
        }
         if(!isset($_POST["passwd"]) || empty($_POST["passwd"])) {
            $data["error"]["message"] = "Veuillez renseignez le pourcentage de la battery";
        }

        if(!isset($_POST["name"]) || empty($_POST["name"])) {
            $data["error"]["message"] = "Veuillez renseignez le pourcentage de la battery";
        }

        if(!isset($_POST["firstName"]) || empty($_POST["firstName"])) {
            $data["error"]["message"] = "Veuillez renseignez le pourcentage de la battery";
        }

        if(!isset($_POST["role"]) || empty($_POST["role"])) {
            $data["error"]["message"] = "Veuillez renseignez le pourcentage de la battery";
        }

        if(!isset($_POST["confirme"]) || empty($_POST["confirme"])) {
            $data["error"]["message"] = "Veuillez renseignez le pourcentage de la battery";
        }

        if(!isset($_POST["date"]) || empty($_POST["date"])) {
            $data["error"]["message"] = "Veuillez renseignez le pourcentage de la battery";
        }

        if (!array_key_exists("error", $data)) {
            $db = new Database();
            $newEmail = $_POST["email"];
            $newPassword = password_hash($_POST["passwd"], PASSWORD_DEFAULT);
            $newName = $_POST["name"];
            $newFirstName = $_POST["firstName"];
            $newRole = $_POST["role"];
            $newConfirme = $_POST["confirme"];
            $newDate = $_POST["date"];
            $id = $this->match["params"]["id"];

            $attrs =[
                "newEmail" => $newEmail,
                "newPassword" => $newPassword,
                "newName" => $newName,
                "newFirstName" => $newFirstName,
                "newRole" => $newRole,
                "newConfirme" => $newConfirme,
                "newDate" => $newDate,
                "id" => $id
            ];
            $req = $db->query("UPDATE users SET email = :newEmail, password  = :newPassword, name = :newName, firstName = :newFirstName, role = :newRole, confirmed = :newConfirme, createdAt = :newDate  where id = :id", $attrs);
            if ($req !=false) {
                $data["success"]["message"] = "Tout est OK";
            }else{
                $data["error"]["message"] = "error 3";
            }
                
        }else{
            $data["error"]["message"] = "error 1";
        }
        
        $data["header"]["admin"] = 1;
        $this->display("admin/updateUsers.html.twig", $data);
    }

    public function delete()
    {
            
            $data["users"] = Users::delete($this->match["params"]["id"]);
            $data["header"]["admin"] = 1;   
            $this->display("Admin/users.html.twig", $data);
    }

}






