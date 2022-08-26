<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\Scooters;
use App\Models\Rides;

class ScootersController extends BaseController 
{
    public function getAll()
    {
        if(!$this->checkAdminAccess()) return;

        $data["scooters"] = Scooters::getAll();
        $this->display("admin/scooters/scooters.html.twig", $data);
    }

    public function getDel(array $data = [])
    {
        if(!$this->checkAdminAccess()) return;

        $scooterId = $this->match["params"]["id"] ?? null;
        $data["scooter"] = Scooters::get($scooterId);

        if(empty($scooterId) || intval($scooterId) <= 0 || !$data["scooter"]) {
            header("Location:" . HOST . "admin/scooters/?boxMsgs=Erreur;error;Trottinette non trouvé.");
            return;
        }

        $this->display("admin/scooters/scootersDel.html.twig", $data);
        
    }

    public function postDel()
    {
        if(!$this->checkAdminAccess()) return;

        $scooterId = $this->match["params"]["id"] ?? null;
        $data["scooter"] = Scooters::get($scooterId);

        if(empty($scooterId) || intval($scooterId) <= 0 || !$data["scooter"]) {
            header("Location:" . HOST . "admin/scooters/?boxMsgs=Erreur;error;Trottinette non trouvé.");
            return;
        }

        $res = Scooters::delete($scooterId);

        if($res) {
            $val = isset($res["boxMsgs"][0]) ? implode(";", $res["boxMsgs"][0]) : "Succès;success;La Trottinette a bien été supprimé.";
            $redirect = HOST . "admin/scooters/?boxMsgs=" . $val;
            header("Location:" . $redirect);
            return;
        }

        if(isset($res["boxMsgs"])) $data["boxMsgs"] = $res["boxMsgs"];
        else $data["boxMsgs"] = [["status" => "Erreur", "class" => "error", "description" => "La Trottinette n'a pas pu être supprimé."]];

        $this->getDel($data);
    }

    public function getAdd(array $data = []) 
    {
        if(!$this->checkAdminAccess()) return;

        $data["scooter"] = Scooters::getAll();

        $this->display("admin/scooters/scootersAdd.html.twig", $data);
    }

    public function postAdd()
    {
        if(!$this->checkAdminAccess()) return;

        $res = Scooters::add($_POST);

        if($res !=null) {
            $val = isset($res["boxMsgs"][0]) ? implode(";", $res["boxMsgs"][0]) : "Succès;success;La trottinette a bien été ajouté.";
            $redirect = HOST . "admin/scooters/?boxMsgs=" . $val;
            header("Location:" . $redirect);
            return;
        }

        if(isset($res["checkedFields"])) $data["checkedFields"] = $res["checkedFields"];
        if(isset($res["boxMsgs"])) $data["boxMsgs"] = $res["boxMsgs"];
        if(isset($res["error"])) $data["form"]["error"] = $res["error"];

        $this->getAdd($data);
    }

    public function getRides(array $data = [])
    {
        if(!$this->checkAdminAccess()) return;

        $scooterId = $this->match["params"]["id"] ?? null;
        $data["scooter"] = Scooters::get($scooterId);

        if(empty($scooterId) || intval($scooterId) <= 0 || !$data["scooter"]) {
            header("Location:" . HOST . "admin/scooters/?boxMsgs=Erreur;error;Trottinette non trouvé.");
            return;
        }

        $data["rides"] = Rides::getAllByScooterId($data["scooter"]["id"]);

        $this->display("admin/scooters/scootersRides.html.twig", $data);
    }

    public function getView()
    {
        if(!$this->checkAdminAccess()) return;

        $scooterId = $this->match["params"]["id"] ?? null;
        $data["scooter"] = Scooters::get($scooterId);

        if(empty($scooterId) || intval($scooterId) <= 0 || !$data["scooter"]) {
            header("Location:" . HOST . "admin/scooters/?boxMsgs=Erreur;error;Trottinette non trouvé.");
            return;
        }

        $this->display("admin/scooters/scootersView.html.twig", $data);
    }

    public function getEdit(array $data = [])
    {
        if(!$this->checkAdminAccess()) return;

        $scooterId = $this->match["params"]["id"] ?? null;
        $data["scooter"] = Scooters::get($scooterId);

        if(empty($scooterId) || intval($scooterId) <= 0 || !$data["scooter"]) {
            header("Location:" . HOST . "admin/scooters/?boxMsgs=Erreur;error;Trottinette non trouvé.");
            return;
        }

        $this->display("admin/scooters/scootersEdit.html.twig", $data);
    }

    public function postEdit()
    {
        if(!$this->checkAdminAccess()) return;

        $scooterId = $this->match["params"]["id"] ?? null;
        $scooter = Scooters::get($scooterId);

        if(empty($scooterId) || intval($scooterId) <= 0 || !$scooter) {
            header("Location:" . HOST . "admin/users/?boxMsgs=Erreur;error;Trottinette non trouvé.");
            return;
        }

        $_POST["id"] = $scooterId;
        Scooters::updateOneById($_POST);
        $this->getEdit();
    }

}