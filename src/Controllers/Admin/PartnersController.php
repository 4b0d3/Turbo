<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\Partners;


class PartnersController extends BaseController 
{
    /**** GET ALL SUBSCRITPTIONS ENTITIES ****/
    public function getAll(array $data = [])
    {
        if(!$this->checkAdminAccess()) return;

        $data["partners"] = Partners::getAll();
        $this->display("admin/partners/partners.html.twig", $data);
    }

    public function getView(array $data = [])
    {
        if(!$this->checkAdminAccess()) return;

        $partnerId = $this->match["params"]["id"] ?? null; 
        $data["partner"] = Partners::get($partnerId);

        if(empty($partnerId) || intval($partnerId) <= 0 || !$data["partner"]) {
            header("Location:" . HOST . "admin/partners/?boxMsgs=Erreur;error;Partenariat non trouvé.");
            return;
        }

        $this->display("admin/partners/partnersView.html.twig", $data);
    }

    public function getEdit(array $data = []) 
    {
        if(!$this->checkAdminAccess()) return;

        $partnerId = $this->match["params"]["id"] ?? null;
        $data["partner"] = Partners::get($partnerId);

        if(empty($partnerId) || intval($partnerId) <= 0 || !$data["partner"]) {
            header("Location:" . HOST . "admin/partners/?boxMsgs=Erreur;error;Partenariat non trouvé.");
            return;
        }

        $this->display("admin/partners/partnersEdit.html.twig", $data);
    }

    public function postEdit()
    {
        if(!$this->checkAdminAccess()) return;

        $partnerId = $this->match["params"]["id"] ?? null;
        $partner = Partners::get($partnerId);

        if(empty($partnerId) || intval($partnerId) <= 0 || !$partner) {
            header("Location:" . HOST . "admin/partners/?boxMsgs=Erreur;error;Partenariat non trouvé.");
            return;
        }

        $_POST["id"] = $partnerId;
        Partners::updateOneById($_POST);
        $this->getEdit();
    }

    public function getDel(array $data = [])
    {
        if(!$this->checkAdminAccess()) return;

        $partnerId = $this->match["params"]["id"] ?? null;
        $data["partner"] = Partners::get($partnerId);

        if(empty($partnerId) || intval($partnerId) <= 0 || !$data["partner"]) {
            header("Location:" . HOST . "admin/partners/?boxMsgs=Erreur;error;Partenariat non trouvé.");
            return;
        }

        $this->display("admin/partners/partnersDel.html.twig", $data);
        
    }

    public function postDel()
    {
        if(!$this->checkAdminAccess()) return;

        $partnerId = $this->match["params"]["id"] ?? null;
        $data["partner"] = Partners::get($partnerId);

        if(empty($partnerId) || intval($partnerId) <= 0 || !$data["partner"]) {
            header("Location:" . HOST . "admin/partners/?boxMsgs=Erreur;error;Partenariat non trouvé.");
            return;
        }

        $res = Partners::delete($partnerId);

        if($res) {
            $val = isset($res["boxMsgs"][0]) ? implode(";", $res["boxMsgs"][0]) : "Succès;success;Le Partenariat a bien été supprimé.";
            $redirect = HOST . "admin/partners/?boxMsgs=" . $val;
            header("Location:" . $redirect);
            return;
        }

        if(isset($res["boxMsgs"])) $data["boxMsgs"] = $res["boxMsgs"];
        else $data["boxMsgs"] = [["status" => "Erreur", "class" => "error", "description" => "Le Partenariat  n'a pas pu être supprimé."]];

        $this->getDel($data);
    }
    
   
}