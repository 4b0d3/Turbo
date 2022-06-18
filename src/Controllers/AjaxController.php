<?php

namespace App\Controllers;

use App\Models\Cart;
use App\Models\Users;

class AjaxController extends BaseController 
{
    public function cart()
    {

        if(!isset($_REQUEST["action"]) 
        || !isset($_REQUEST["id"])
        || intval($_REQUEST["id"]) < 0) 
        { echo null; return; }
        $productId = $_GET["id"];

        switch($_REQUEST["action"]) {
            case "add":
                echo Cart::addProductOne($productId) ? ($this->user->isAuthenticated() ? Cart::getProductQuantity($productId) : Cart::getProductQuantity($productId)+1) : 0;
                return;
            case "delete":
                echo Cart::delProductOne($productId) ? ($this->user->isAuthenticated() ? Cart::getProductQuantity($productId) : Cart::getProductQuantity($productId)-1) : 0;
                return;
            case "deleteAll":
                echo Cart::delProductOne($productId, Cart::getProductQuantity($productId)) ? $productId : 0;
                return;
        }
        echo "null";
        return;
    }

    public function address()
    {

        if(!isset($_REQUEST["action"]) 
        || !isset($_REQUEST["id"])
        || intval($_REQUEST["id"]) < 0) 
        { echo null; return; }
        $idAddress = $_GET["id"];

        switch($_REQUEST["action"]) {
            case "changeFavAddress":
                echo Users::setFavAddresss($this->user->get("id"), $idAddress);
                return;
        }
        echo "null";
        return;
    }
}