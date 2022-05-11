<?php

namespace App\Controllers;

use App\Database\Database;
use App\Models\Users;

class UserController extends BaseController 
{

    public function showInformations() 
    {
        if($this->user->isAnonymous()) {
            header("Location:" . $this->router->generate("login", ["lang" => $this->match["params"]["lang"] ?? "fr"]));
            exit;
        }

        $this->display("user/informations.html.twig");
    }

    public function editInformations()
    {
        if($this->user->isAnonymous()) {
            header("Location:" . $this->router->generate("login", ["lang" => $this->match["params"]["lang"] ?? "fr"]));
            exit;
        }


        $this->display("user/informations.html.twig");
    }

    public function showOrders() 
    {
        $this->display("user/orders.html.twig");
    }
    
    public function showReturns() 
    {
        $this->display("user/returns.html.twig");
    }

    public function showRides() 
    {
        $this->display("user/rides.html.twig");
    }

    public function showChangePassword() 
    {
        $this->display("user/change-password.html.twig");
    }

    public function editChangePassword() 
    {
        if($this->user->isAnonymous()) {
            header("Location:" . $this->router->generate("login", ["lang" => $this->match["params"]["lang"] ?? "fr"]));
            exit;
        }

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
        $this->display("user/addresses.html.twig");
    }

    public function showPaymentMethods() 
    {
        $this->display("user/payment-methods.html.twig");
    }

    public function showNotifications() 
    {
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
        header("Location:". HOST);
    }

}