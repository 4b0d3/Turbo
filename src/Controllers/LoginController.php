<?php

namespace App\Controllers;

use App\Database\Database;

class LoginController extends BaseController {
    public function get() {
        $this->display("login.html.twig", ["user" => "ok"]);
    }

    public function post() {

        if(!isset($_POST["email"]) || empty($_POST["email"])) {
            $data["error"]["message"] = "Veuillez renseignez une addresse mail";
        } else if(!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
            $data["error"]["message"] = "Format du mail incorrect";
        } else {
            $data["form"]["email"] = $_POST["email"];
        }

        if(!isset($_POST["password"]) || empty($_POST["password"])) {
            $data["error"]["message"] = "Veuillez renseignez un mot de passe";
        }
        
        if(!array_key_exists("error", $data)) {
            $db = new Database();
            $user = $db->queryOne("SELECT * from users where email = ? AND password = ?", [$_POST["email"], password_hash($_POST["password"], PASSWORD_DEFAULT)]);
            if($user != false && isset($user["id"])) {
                $_SESSION["id"] = $user["id"];
            } else {
                $data["error"]["message"] = "Email ou mot de passe invalides";
            }
        }

        $this->display("login.html.twig", $data);
    }

}