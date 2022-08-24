<?php

namespace App\Controllers;

use App\Database\Database;
use App\Models\Commands;
use App\Models\Products;
use App\Models\Users;
use App\Models\Roles;
use App\Models\Subscriptions;
use App\Models\Juicers;

class UserController extends BaseController 
{
    /* LOGIN */
    public function getLogin(array $data = []) 
    {
        if($this->user->isAuthenticated()) {
            header("Location:" . $this->router->generate("myaccount", ["lang" => $this->lang]));
            return true;
        }
        
        isset($_GET["r"]) ? $data["r"] = $_GET["r"] : ""; 
        $this->display("site/login.html.twig", $data);
    }

    public function postLogin() 
    {
        if($this->user->isAuthenticated()) {
            header("Location:" . $this->router->generate("myaccount", ["lang" => $this->lang]));
            return true;
        }

        $res = Users::login($_POST);
        $email = $_POST['email'];

        if($res["status"]) {
            $this->verifSubValidity($email);
            $val = isset($res["boxMsgs"][0]) ? implode(";", $res["boxMsgs"][0]) : "Succès;success;Connecté.";
            $redirect = $this->urls["BASEURL"] . (isset($_GET["r"]) ? $_GET["r"] : "?boxMsgs=" . $val);
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


            // TODO REDIRECTION ENVOIE MAIL DE CONFIRMATION
            $email = $_POST['email'];

            $user = Users::getByMail($email);
            $token = Users::newToken($user["id"]);
            
            // $new = Users::addVerifToken($token, $email);

            $this->emailVerif($email, $token);

            //


            $data["verif"]= $email;
            $this->display("user/verification.html.twig", $data);
            return;
            // $val = isset($res["boxMsgs"][0]) ? implode(";", $res["boxMsgs"][0]) : "Succès;success;L'utilisateur a bien été créé.";
            // $redirect = $this->urls["BASEURL"] . "?boxMsgs=" . $val;
            // header("Location:" . $redirect);
            // return;
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

    public function emailVerif($email, $token){
        $sujet = "Activer votre compte" ;
        $entete = "From: inscription@turbo.com" ;

        $message = 'Bienvenue sur Turbo.com,
        Pour activer votre compte, veuillez cliquer sur le lien ci-dessous
        ou copier/coller dans votre navigateur Internet.
        https://turbo.com/verify?email='.urlencode($email).'/token='.urlencode($token).'/'.'
        ---------------
        Ceci est un mail automatique, Merci de ne pas y répondre.';
 
        mail($email, $sujet, $message, $entete) ; // Envoi du mail
    }

    public function getVerification(array $data = []){
        
        $email = $this->match["params"]["email"] ?? null;
        $token = $this->match["params"]["token"] ?? null;
        
        $new = Users::checkAccount($email);
        
        if($new != null){
            $tokenbdd = $new['token'];
            $confirmed =  $new['confirmed'];
        }

        if($confirmed == '1'){
            //"Votre compte est déjà actif !"
            $data["deja"]= $email;
            $this->display("user/verification.html.twig", $data);
        }else {
            if($token == $tokenbdd){
                //"Votre compte a bien été activé !"
                $data["bien"]= $email;
                Users::verifAccount($email);
                $this->display("user/verification.html.twig", $data);
            }else{
                //"Erreur ! Votre compte ne peut être activé..."
                $data["error"]= $email;
                $this->display("user/verification.html.twig", $data);
            }
        }

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

    public function showOrders(array $data = []) 
    {
        if($this->checkAnonymous()) return;

        $data["commands"] = Commands::getAllNotReturned($this->user->get("id"));

        if($data["commands"] != null) {
            foreach($data["commands"] as &$command) {
                $products = explode(";", $command["products"]);
                $newProducts = [];
                foreach($products as $key => $product) {
                    $productId = explode(":", $product)[0];
                    array_push($newProducts, Products::get(intval($productId)));
                }
                array_pop($newProducts);
                $command["products"] = $newProducts;
            }
        }

        $this->display("user/orders.html.twig", $data);
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
                $res = Users::updateOneById(["id" => $this->user->get("id"), "password" => $_POST["password"]]); 
                if(!$res) { $data["msgBoxes"][] = ["status" => "error", "description" => "Problèmes lors du changement du mot de passe, le mot de passe n'a pas été changé !"]; }
                else  {$data["msgBoxes"][] = ["status" => "success", "description" => "Le mot de passe à bien été changé !"]; }
            } else {
                $data["msgBoxes"][] = ["status" => "error", "description" => "L'ancien mot de passe est invalide !"];
            }
        }

        $this->display("user/change-password.html.twig", $data);
    }

    public function getAddresses(array $data = []) 
    {
        if($this->checkAnonymous()) return;

        $data["addresses"] = Users::getAllAddresses($this->user->get("id"));
        $this->display("user/addresses.html.twig", $data);
    }

    public function postAddresses() 
    {
        if($this->checkAnonymous()) return;
        if(!isset($_POST["country"]) || empty($_POST["country"]) ||
        !isset($_POST["city"]) || empty($_POST["city"]) ||
        !isset($_POST["address"]) || empty($_POST["address"]) ||
        !isset($_POST["zipcode"]) || empty($_POST["zipcode"]) ||
        !isset($_POST["isMain"])) { $this->getAddresses(); return; }

        if(!isset($_POST["additional"]) || empty($_POST["additional"])) { $_REQUEST["additional"] = "" ; }

        Users::addAddress($this->user->get("id"), $_REQUEST);
        $this->getAddresses(); return;
    }

    public function deleteAddresses() {
        if(!isset($_POST["idAddress"]) || empty($_POST["idAddress"])) { header("Location:" . $this->urls["BASEURL"] . "my-account/addresses/"); return; }
        Users::deleteAddress($_POST["idAddress"]);
        header("Location:" . $this->urls["BASEURL"] . "my-account/addresses/"); return;
    }

    public function getSubscriptions(array $data = []) 
    {
        if($this->checkAnonymous()) return;
        if(!empty($this->user->get("sub"))) {
            $data["page"]["sub"] = Subscriptions::get($this->user->get("sub"));
        }

        $this->display("user/subscriptions.html.twig", $data);
    }

    public function postSubscriptions() {
        if($this->checkAnonymous()) return;

        Users::deleteSub($this->user->get("id"));

        header("Location:" . $this->urls["BASEURL"] . "my-account/subscriptions/");
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


    public function getinvoices(array $data = []) 
    {
        if($this->checkAnonymous()) return;
        $data["invoices"] = Users::getAllInvoices($this->user->get("id"));
        
        $this->display("user/invoices.html.twig", $data);
    }

    public static function verifSubValidity($email){ 
        $user = Users::getByMail($email);
        $tmp = date('Y-m-d');
        $tmstp1 = strtotime($user["subExpire"]);
        $tmstp2 = strtotime($tmp);
        if ($tmstp1 < $tmstp2){
            Users::deleteSub($user["id"]);
        }
    }

    public function getPartner(){
        if($this->checkAnonymous()) return;
        $this->display("user/partners.html.twig");

    }

    public function postPartner(){
        if($this->checkAnonymous()) return;

        $data = [];

        if(!isset($_POST["name"]) || empty($_POST["name"])) {
            $data["error"]["name"] = "Veuillez renseigner le nom";
        }
        if(!isset($_POST["description"]) || empty($_POST["description"])) {
            $data["error"]["description"] = "Veuillez renseigner une description";
        }
        if(!isset($_POST["price"]) || empty($_POST["price"])) {
            $data["error"]["price"] = "Veuillez renseigner un prix";
        }
        if(!isset($_POST["promo"]) || empty($_POST["promo"])) {
            $data["error"]["promo"] = "Veuillez renseigner un code de promotion";
        }

        $PartnerInfos = [
            "name" => $_POST["name"],
            "description" => $_POST["description"],
            "price" => $_POST["price"],
            "promoCode" => $_POST["promo"],
        ];

        if(!array_key_exists("error", $data)) {
                $res = Users:: addPartner($PartnerInfos);
                if(!$res) { $data["msgBoxes"][] = ["status" => "error", "description" => "Problèmes lors d'envoie"]; }
                else  {$data["msgBoxes"][] = ["status" => "success", "description" => "La demande de partenariat a été envoyée !"]; }
            } else {
                $data["msgBoxes"][] = ["status" => "error", "description" => "Le formulaire est invalide !"];
            }
        

        $this->display("user/partners.html.twig", $data);
    }

    public function getAllJuicer(){
        if($this->checkAnonymous()) return;
        $data["scooters"] = Juicers::getAll(15);
        $this->display("user/juicers.html.twig", $data);
    }

    public function getCharged(){
        if(!$this->checkjuicerAccess()) return;
        $scooterId = $this->match["params"]["id"] ?? null;
        $data["scooter"] = Juicers::get($scooterId);

        if(empty($scooterId) || intval($scooterId) <= 0 || !$data["scooter"]) {
            header("Location:" . HOST . "juicers/scooters/?boxMsgs=Erreur;error;Trottinette non trouvé.");
            return;
        }
        $this->display("user/confirmCharging.html.twig", $data);
    }

    public function postCharged(){
        if(!$this->checkjuicerAccess()) return;

        $scooterId = $this->match["params"]["id"] ?? null;
        $data["scooter"] = Juicers::get($scooterId);

        if(empty($scooterId) || intval($scooterId) <= 0 || !$data["scooter"]) {
            header("Location:" . HOST . "juicers/scooters/?boxMsgs=Erreur;error; Trottinette non trouvé.");
            return;
        }

        $res = Juicers::chargeScooter($scooterId);

        if($res) {
            $val = isset($res["boxMsgs"][0]) ? implode(";", $res["boxMsgs"][0]) : "Succès;success;La Trottinette a bien été Chargée.";
            $redirect = HOST . "juicers/scooters/?boxMsgs=" . $val;
            header("Location:" . $redirect);
            return;
        }

        $data["boxMsgs"] = [["status" => "Erreur", "class" => "error", "description" => "Le produit n'a pas pu être supprimé."]];
    }

}