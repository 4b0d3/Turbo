<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\Invoices;


class InvoicesController extends BaseController 
{
    /**** GET ALL INVOICES ENTITIES ****/
    public function getAll(array $data = [])
    {
        if(!$this->checkAdminAccess()) return;

        $data["invoices"] = Invoices::getAll();
        $this->display("admin/invoices/invoices.html.twig", $data);
    }

    public function getView(array $data = [])
    {
        if(!$this->checkAdminAccess()) return;

        $invoiceId = $this->match["params"]["id"] ?? null; 
        $data["invoice"] = Invoices::get($invoiceId);

        if(empty($invoiceId) || intval($invoiceId) <= 0 || !$data["invoice"]) {
            header("Location:" . HOST . "admin/invoices/?boxMsgs=Erreur;error;Facture non trouvé.");
            return;
        }

        $this->display("admin/invoices/invoicesView.html.twig", $data);
    }

    public function getDel(array $data = [])
    {
        if(!$this->checkAdminAccess()) return;

        $invoiceId = $this->match["params"]["id"] ?? null;
        $data["invoice"] = Invoices::get($invoiceId);

        if(empty($invoiceId) || intval($invoiceId) <= 0 || !$data["invoice"]) {
            header("Location:" . HOST . "admin/invoices/?boxMsgs=Erreur;error;Facture non trouvé.");
            return;
        }

        $this->display("admin/invoices/invoicesDel.html.twig", $data);
        
    }

    public function postDel()
    {
        if(!$this->checkAdminAccess()) return;

        $invoiceId = $this->match["params"]["id"] ?? null;
        $data["invoice"] = Invoices::get($invoiceId);

        if(empty($invoiceId) || intval($invoiceId) <= 0 || !$data["invoice"]) {
            header("Location:" . HOST . "admin/invoices/?boxMsgs=Erreur;error;Facture non trouvé.");
            return;
        }

        $res = Invoices::delete($invoiceId);

        if($res) {
            $val = isset($res["boxMsgs"][0]) ? implode(";", $res["boxMsgs"][0]) : "Succès;success;La Facture a bien été supprimé.";
            $redirect = HOST . "admin/invoices/?boxMsgs=" . $val;
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

        $invoiceId = $this->match["params"]["id"] ?? null;
        $data["invoice"] = Invoices::get($invoiceId);

        if(empty($invoiceId) || intval($invoiceId) <= 0 || !$data["invoice"]) {
            header("Location:" . HOST . "admin/invoices/?boxMsgs=Erreur;error;Facture non trouvé.");
            return;
        }

        $this->display("admin/invoices/invoicesEdit.html.twig", $data);
    }

    public function postEdit()
    {
        if(!$this->checkAdminAccess()) return;

        $invoiceId = $this->match["params"]["id"] ?? null;
        $invoice = Invoices::get($invoiceId);

        if(empty($invoiceId) || intval($invoiceId) <= 0 || !$invoice) {
            header("Location:" . HOST . "admin/invoices/?boxMsgs=Erreur;error;Facture non trouvé.");
            return;
        }

        $_POST["id"] = $invoiceId;
        Invoices::updateOneById($_POST);
        $this->getEdit();
    }
   
} 