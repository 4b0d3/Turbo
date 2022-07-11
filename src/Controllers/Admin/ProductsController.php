<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\Products;

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

        $userId = $this->match["params"]["id"] ?? null;
        $data["userInfo"] = Users::get($userId);

        if(empty($userId) || intval($userId) <= 0 || !$data["userInfo"]) {
            header("Location:" . HOST . "admin/users/?boxMsgs=Erreur;error;Utilisateur non trouvé.");
            return;
        }

        $res = Users::delete($userId);

        if($res) {
            $val = isset($res["boxMsgs"][0]) ? implode(";", $res["boxMsgs"][0]) : "Succès;success;L'utilisateur a bien été supprimé.";
            $redirect = HOST . "admin/users/?boxMsgs=" . $val;
            header("Location:" . $redirect);
            return;
        }

        if(isset($res["boxMsgs"])) $data["boxMsgs"] = $res["boxMsgs"];
        else $data["boxMsgs"] = [["status" => "Erreur", "class" => "error", "description" => "L'utilisateur n'a pas pu être supprimé."]];

        $this->getDel($data);
    }
   
}