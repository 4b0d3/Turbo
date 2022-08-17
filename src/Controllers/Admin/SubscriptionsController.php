<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\Subscriptions;
use Stripe\Subscription;

class SubscriptionsController extends BaseController 
{
    /**** GET ALL SUBSCRITPTIONS ENTITIES ****/
    public function getAll(array $data = [])
    {
        if(!$this->checkAdminAccess()) return;

        $data["subscriptions"] = Subscriptions::getAll();
        $this->display("admin/subscriptions/subscriptions.html.twig", $data);
    }

    public function getAdd(array $data = []){
        if(!$this->checkAdminAccess()) return;

        $this->display("admin/subscriptions/subscriptionsAdd.html.twig", $data);

    }

    public function postAdd(array $data = [])
    {
        if(!$this->checkAdminAccess()) return;

        if(!isset($_POST["name"]) || empty($_POST["name"]) || !isset($_POST["description"]) || !isset($_POST["price"]) || $_POST["price"] == null || !isset($_POST["title"])) {
            $this->getAdd(["boxMsgs" => [["status" => "Erreur", "class" => "Error", "description" => "Il manque des informations"]]]);
            return;
        }

        $SubInfos = [
            "name" => $_POST["name"],
            "description" => $_POST["description"],
            "price" => $_POST["price"],
            "title" => $_POST["title"],
        ];

        $res = Subscriptions::add($SubInfos);

        $this->getAdd($data);
    }

    public function getView(array $data = [])
    {
        if(!$this->checkAdminAccess()) return;

        $subscriptionId = $this->match["params"]["id"] ?? null; 
        $data["subscription"] = Subscriptions::get($subscriptionId);

        if(empty($subscriptionId) || intval($subscriptionId) <= 0 || !$data["subscription"]) {
            header("Location:" . HOST . "admin/subscriptions/?boxMsgs=Erreur;error;Abonnement non trouvé.");
            return;
        }

        $this->display("admin/subscriptions/subscriptionsView.html.twig", $data);
    }

    public function getDel(array $data = [])
    {
        if(!$this->checkAdminAccess()) return;

        $subscriptionId = $this->match["params"]["id"] ?? null;
        $data["subscription"] = Subscriptions::get($subscriptionId);

        if(empty($subscriptionId) || intval($subscriptionId) <= 0 || !$data["subscription"]) {
            header("Location:" . HOST . "admin/subscriptions/?boxMsgs=Erreur;error;Abonnement non trouvé.");
            return;
        }

        $this->display("admin/subscriptions/subscriptionsDel.html.twig", $data);
        
    }

    public function postDel()
    {
        if(!$this->checkAdminAccess()) return;

        $subscriptionId = $this->match["params"]["id"] ?? null;
        $data["subscription"] = Subscriptions::get($subscriptionId);

        if(empty($subscriptionId) || intval($subscriptionId) <= 0 || !$data["subscription"]) {
            header("Location:" . HOST . "admin/subscriptions/?boxMsgs=Erreur;error;Abonnement non trouvé.");
            return;
        }

        $res = Subscriptions::delete($subscriptionId);

        if($res) {
            $val = isset($res["boxMsgs"][0]) ? implode(";", $res["boxMsgs"][0]) : "Succès;success;L'Abonnement a bien été supprimé.";
            $redirect = HOST . "admin/subscriptions/?boxMsgs=" . $val;
            header("Location:" . $redirect);
            return;
        }

        if(isset($res["boxMsgs"])) $data["boxMsgs"] = $res["boxMsgs"];
        else $data["boxMsgs"] = [["status" => "Erreur", "class" => "error", "description" => "L'Abonnement n'a pas pu être supprimé."]];

        $this->getDel($data);
    }

    public function getEdit(array $data = []) 
    {
        if(!$this->checkAdminAccess()) return;

        $subscriptionId = $this->match["params"]["id"] ?? null;
        $data["subscription"] = Subscriptions::get($subscriptionId);

        if(empty($subscriptionId) || intval($subscriptionId) <= 0 || !$data["subscription"]) {
            header("Location:" . HOST . "admin/subscriptions/?boxMsgs=Erreur;error;Abonnement non trouvé.");
            return;
        }

        $this->display("admin/subscriptions/subscriptionsEdit.html.twig", $data);
    }

    public function postEdit()
    {
        if(!$this->checkAdminAccess()) return;

        $subscriptionId = $this->match["params"]["id"] ?? null;
        $subscription = Subscriptions::get($subscriptionId);

        if(empty($subscriptionId) || intval($subscriptionId) <= 0 || !$subscription) {
            header("Location:" . HOST . "admin/subscriptions/?boxMsgs=Erreur;error;Produit non trouvé.");
            return;
        }

        $_POST["id"] = $subscriptionId;
        Subscriptions::updateOneById($_POST);
        $this->getEdit();
    }
   
} 