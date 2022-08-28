<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\Products;
use App\Models\Returns;

class ProductsController extends BaseController 
{
    /**** GET ALL SUBSCRITPTIONS ENTITIES ****/
    public function getAll(array $data = [])
    {
        if(!$this->checkAdminAccess()) return;

        $data["products"] = Products::getAll();
        $this->display("admin/products/products.html.twig", $data);
    }

    /**** GET PRODUCT ADD FORM ****/
    public function getAdd(array $data = []) 
    {
        if(!$this->checkAdminAccess()) return;

        $this->display("admin/products/productsAdd.html.twig", $data);
    }

    public function postAdd(array $data = [])
    {
        if(!$this->checkAdminAccess()) return;

        if(!isset($_POST["name"]) || empty($_POST["name"]) || !isset($_POST["description"]) || !isset($_POST["price"]) || $_POST["price"] == null || !isset($_POST["isPromotion"]) || !isset($_POST["promotion"]) || !isset($_POST["quantity"])) {
            $this->getAdd(["boxMsgs" => [["status" => "Erreur", "class" => "Error", "description" => "Il manque des informations"]]]);
            return;
        }

        $productInfos = [
            "name" => $_POST["name"],
            "description" => $_POST["description"],
            "price" => $_POST["price"],
            "isPromotion" => $_POST["isPromotion"],
            "promotion" => $_POST["promotion"],
            "quantity" => $_POST["quantity"],
        ];

        $res = Products::add($productInfos);

        if($res["status"]) {
            $val = isset($res["boxMsgs"][0]) ? implode(";", $res["boxMsgs"][0]) : "Succès;success;Le produit a bien été ajouté.";
            $redirect = HOST . "admin/products/?boxMsgs=" . $val;
            header("Location:" . $redirect);
            return;
        }

        $data["checkedFields"] = $productInfos;

        if(isset($res["boxMsgs"])) $data["boxMsgs"] = $res["boxMsgs"];

        $this->getAdd($data);
    }

    public function getView(array $data = [])
    {
        if(!$this->checkAdminAccess()) return;

        $productId = $this->match["params"]["id"] ?? null;
        $data["product"] = Products::get($productId);

        if(empty($productId) || intval($productId) <= 0 || !$data["product"]) {
            header("Location:" . HOST . "admin/products/?boxMsgs=Erreur;error;Produit non trouvé.");
            return;
        }

        $this->display("admin/products/productsView.html.twig", $data);
    }

    public function getEdit(array $data = []) {
        if(!$this->checkAdminAccess()) return;

        $productId = $this->match["params"]["id"] ?? null;
        $data["product"] = Products::get($productId);

        if(empty($productId) || intval($productId) <= 0 || !$data["product"]) {
            header("Location:" . HOST . "admin/products/?boxMsgs=Erreur;error;Produit non trouvé.");
            return;
        }

        $this->display("admin/products/productsEdit.html.twig", $data);
    }

    public function postEdit()
    {
        if(!$this->checkAdminAccess()) return;

        $productId = $this->match["params"]["id"] ?? null;
        $product = Products::get($productId);

        if(empty($productId) || intval($productId) <= 0 || !$product) {
            header("Location:" . HOST . "admin/products/?boxMsgs=Erreur;error;Produit non trouvé.");
            return;
        }

        $_POST["id"] = $productId;
        Products::updateOneById($_POST);
        $this->getEdit();
    }

    public function getDel(array $data = [])
    {
        if(!$this->checkAdminAccess()) return;

        $productId = $this->match["params"]["id"] ?? null;
        $data["product"] = Products::get($productId);

        if(empty($productId) || intval($productId) <= 0 || !$data["product"]) {
            header("Location:" . HOST . "admin/users/?boxMsgs=Erreur;error;Produit non trouvé.");
            return;
        }

        $this->display("admin/products/productsDel.html.twig", $data);
        
    }

    public function postDel()
    {
        if(!$this->checkAdminAccess()) return;

        $productId = $this->match["params"]["id"] ?? null;
        $data["product"] = Products::get($productId);

        if(empty($productId) || intval($productId) <= 0 || !$data["product"]) {
            header("Location:" . HOST . "admin/users/?boxMsgs=Erreur;error;Produit non trouvé.");
            return;
        }

        $res = Products::delete($productId);

        if($res) {
            $val = isset($res["boxMsgs"][0]) ? implode(";", $res["boxMsgs"][0]) : "Succès;success;Le produit a bien été supprimé.";
            $redirect = HOST . "admin/products/?boxMsgs=" . $val;
            header("Location:" . $redirect);
            return;
        }

        $data["boxMsgs"] = [["status" => "Erreur", "class" => "error", "description" => "Le produit n'a pas pu être supprimé."]];

        $this->getDel($data);
    }

    public function getAllReturns(){
        if(!$this->checkAdminAccess()) return;
        $data["returns"] = Returns::allReturns();
        $this->display("admin/products/returns.html.twig", $data);
    }

    public function getValide(){
        if(!$this->checkAdminAccess()) return;
        $returnId = $this->match["params"]["id"] ?? null;
        $data["return"] = Returns::getOrder($returnId);
        if(empty($returnId) || intval($returnId) <= 0 || !$data["return"]) {
            header("Location:" . HOST . "admin/returns/?boxMsgs=Erreur;error;Commande non trouvé.");
            return;
        }
        $this->display("admin/products/valideReturn.html.twig", $data);
    }

    public function postValide(){
        
        if(!$this->checkAdminAccess()) return;
        $returnId = $this->match["params"]["id"] ?? null;
        $data["return"] = Returns::get($returnId);

        if(empty($returnId) || intval($returnId) <= 0 || !$data["return"]) {
            header("Location:" . HOST . "admin/returns/?boxMsgs=Erreur;error; Commande non trouvé.");
            return;
        }

        $res = Returns::valideReturn($returnId);

        if($res) {
            $val = isset($res["boxMsgs"][0]) ? implode(";", $res["boxMsgs"][0]) : "Succès;success;Le retour a bien été programé.";
            $redirect = HOST . "admin/returns/?boxMsgs=" . $val;
            header("Location:" . $redirect);
            return;
        }

        $data["boxMsgs"] = [["status" => "Erreur", "class" => "error", "description" => "Le retour n'a pas pu être effictué."]];

    }
   
}