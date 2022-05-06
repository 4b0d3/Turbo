<?php

namespace App\Controllers;

use App\Database\Database;

class UserPassController extends BaseController {
    public function get() {
        $username = $this->user->get("firstName");
        if ($username != null) {
            $data["success"]["username"] = $username;
        }else{
            $data["error"]["message"] = "error ";
        }
        dump("Hello");
        $this->display("userPass.html.twig", $data);

    }

    public function post() {

        $data = [];

        
       
        if(!isset($_POST["password"]) || empty($_POST["password"])) {
            $data["error"]["message"] = "Veuillez renseignez un mot de passe";
        }
        if(!isset($_POST["newpassword"]) || empty($_POST["newpassword"])) {
            $data["error"]["message"] = "Veuillez renseignez un mot de passe";
        }

        if(!array_key_exists("error", $data)) {
           
            $db = new Database();
            $attrs = ["email"=>$this->user->get("email")];
            $user = $db->queryOne("SELECT id, password from users where email = :email ", $attrs);

            if($user != false && isset($user["id"])){
                $passwordHash = $user["password"];
                $password = $_POST["password"];
                if(password_verify($password, $passwordHash)) {

                    $attrs["newpassword"] = password_hash($_POST["newpassword"], PASSWORD_DEFAULT);
                    $req = $db->query("UPDATE users SET password = :newpassword where email = :email", $attrs);
                    if ($req !=false) {
                        $data["success"]["message"] = "Le mot de passe a été modifié";
                    }else{
                        $data["error"]["message"] = "error 3";
                    }
                }else {
                    $data["error"]["message"] = "password doesnt match";
                }
            }else{
                $data["error"]["message"] = "error 1";
            }
        }

        $this->display("userPass.html.twig", $data);
    }

}