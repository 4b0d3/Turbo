<?php

namespace App\Controllers;

use App\Database\Database;
use App\Models\Users;
use App\Models\Roles;

class UserController extends BaseController 
{
    /* LOGIN */
    public function getLogin(array $data = []) 
    {
        if($this->user->isAuthenticated()) {
            header("Location:" . $this->router->generate("myaccount", ["lang" => $this->lang]));
            return true;
        }

        $this->display("site/login.html.twig", $data);
    }

    public function postLogin() 
    {
        if($this->user->isAuthenticated()) {
            header("Location:" . $this->router->generate("myaccount", ["lang" => $this->lang]));
            return true;
        }

        $res = Users::login($_POST);

        if($res["status"]) {
            $val = isset($res["boxMsgs"][0]) ? implode(";", $res["boxMsgs"][0]) : "Succès;success;Connecté.";
            $redirect = $this->urls["BASEURL"] . "?boxMsgs=" . $val;
            header("Location:" . $redirect);
            return;
        }

        if(isset($res["form"]["checkedFields"])) $data["form"]["checkedFields"] = $res["form"]["checkedFields"];
        if(isset($res["boxMsgs"])) $data["boxMsgs"] = $res["boxMsgs"];
        if(isset($res["form"]["error"])) $data["form"]["error"] = $res["form"]["error"];

        $this->getLogin($data);
    }

    /* REGISTER */
    public function getRegister(array $data = []) 
    {
        if($this->user->isAuthenticated()) {
            header("Location:" . $this->router->generate("myaccount", ["lang" => $this->lang]));
            return true;
        }

        $this->display("site/register.html.twig", $data);
    }

    public function postRegister() 
    {
        if($this->user->isAuthenticated()) {
            header("Location:" . $this->router->generate("myaccount", ["lang" => $this->lang]));
            return true;
        }

        $_POST["role"] = Roles::getId("user");
        $res = Users::add($_POST);

        if($res["status"]) {
            // TODO REDIRECTION MAIL DE CONFIRMATION
            $val = isset($res["boxMsgs"][0]) ? implode(";", $res["boxMsgs"][0]) : "Succès;success;L'utilisateur a bien été créé.";
            $redirect = $this->urls["BASEURL"] . "?boxMsgs=" . $val;
            header("Location:" . $redirect);
            return;
        }

        if(isset($res["form"]["checkedFields"])) $data["form"]["checkedFields"] = $res["form"]["checkedFields"];
        if(isset($res["boxMsgs"])) $data["boxMsgs"] = $res["boxMsgs"];
        if(isset($res["form"]["error"])) $data["form"]["error"] = $res["form"]["error"];

        $this->getRegister($data);
    }


    public function checkAnonymous()
    {
        if(!$this->user->isAuthenticated())  {
            header("Location:" . $this->router->generate("login", ["lang" => $this->lang]));
            return true;
        }
        return false;
    }

    public function showInformations() 
    {
        if($this->checkAnonymous()) return;

        $this->display("user/informations.html.twig");
    }

    public function editInformations()
    {
        if($this->checkAnonymous()) return;


        $this->display("user/informations.html.twig");
    }

    public function showOrders() 
    {
        if($this->checkAnonymous()) return;
        $this->display("user/orders.html.twig");
    }
    
    public function showReturns() 
    {
        if($this->checkAnonymous()) return;
        $this->display("user/returns.html.twig");
    }

    public function showRides() 
    {
        if($this->checkAnonymous()) return;
        $this->display("user/rides.html.twig");
    }

    public function showChangePassword() 
    {
        if($this->checkAnonymous()) return;
        $this->display("user/change-password.html.twig");
    }

    public function editChangePassword() 
    {
        if($this->checkAnonymous()) return;

        $data = [];

        if(!isset($_POST["oldPassword"]) || empty($_POST["oldPassword"])) {
            $data["error"]["oldPassword"] = "Veuillez renseigner votre ancien mot de passe";
        }
        if(!isset($_POST["password"]) || empty($_POST["password"])) {
            $data["error"]["password"] = "Veuillez renseigner un mot de passe";
        }
        if(!isset($_POST["confirmPassword"]) || empty($_POST["confirmPassword"])) {
            $data["error"]["confirmPassword"] = "Veuillez confirmer votre nouveaux mot de passe";
        }

        if(!array_key_exists("error", $data)) {
            if(password_verify($_POST["oldPassword"], $this->user->get("password"))) {
                $res = Users::updateOneById($this->user->get("id"), ["password" => password_hash($_POST["password"], PASSWORD_DEFAULT)]); // TODO CHANGE PASSWORD DEFAULT
                if(!$res) { $data["msgBoxes"][] = ["status" => "error", "description" => "Problèmes lors du changement du mot de passe, le mot de passe n'a pas été changé !"]; }
                else  {$data["msgBoxes"][] = ["status" => "success", "description" => "Le mot de passe à bien été changé !"]; }
            } else {
                $data["msgBoxes"][] = ["status" => "error", "description" => "L'ancien mot de passe est invalide !"];
            }
        }

        $this->display("user/change-password.html.twig", $data);
    }

    public function showAddresses() 
    {
        if($this->checkAnonymous()) return;
        $this->display("user/addresses.html.twig");
    }

    public function showPaymentMethods() 
    {
        if($this->checkAnonymous()) return;
        $this->display("user/payment-methods.html.twig");
    }

    public function showNotifications() 
    {
        if($this->checkAnonymous()) return;
        $this->display("user/notifications.html.twig");
    }

    public function disconnect()
    {
        // Détruit toutes les variables de session
        $_SESSION = array();

        // Si vous voulez détruire complètement la session, effacez également
        // le cookie de session.
        // Note : cela détruira la session et pas seulement les données de session !
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        // Finalement, on détruit la session.
        session_destroy();
        header("Location:". $this->urls["BASEURL"]);
    }

}