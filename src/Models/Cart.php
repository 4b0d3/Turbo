<?php 

namespace App\Models;

use App\Database\Database;
use App\Entity\User;

class Cart {
    public static function getAllProducts()
    {
        $user = new User();

        if($user->isAuthenticated()) {
            $db = new Database();
            $q = "SELECT p.*, p.quantity as pQuantity, i.quantity FROM products AS p INNER JOIN incart AS i ON p.id = i.idProduct WHERE i.idUser = ?";
            $res = $db->queryAll($q, [$user->get("id")]) ?: null;
            return $res;
        } else {
            $products = isset($_COOKIE["cart"]) ? json_decode(stripslashes($_COOKIE['cart']), true) : [];
            $res = [];
            foreach($products as $id => $quantity) {
                $product = Products::get($id);
                $product["pQuantity"] = $product["quantity"];
                $product["quantity"] = $quantity;
                array_push($res, $product);
            }
            return $res ?: null;
        }
    }

    public static function addProductOne($id, int $quantity = 1)
    {
        $user = new User();

        if($user->isAuthenticated()) {
            $db = new Database();
            $q = "SELECT * FROM incart AS i WHERE i.idUser = ? and i.idProduct = ?";
            
            $q = $db->queryOne($q, [$user->get("id"), $id]) != null ? 
                "UPDATE incart AS i SET i.quantity = i.quantity+:quantity WHERE i.idUser = :idUser and i.idProduct = :idProduct" :
                "INSERT INTO incart(idUser, idProduct, quantity) VALUES (:idUser, :idProduct, :quantity)";
            $res = $db->query($q, [ "quantity" => $quantity, "idUser" => $user->get("id"), "idProduct" => $id]);
            return $res;
        } else {
            $expire = time()+60*60*24*30; // 30 jours
            $products = isset($_COOKIE["cart"]) ? json_decode(stripslashes($_COOKIE['cart']), true) : [];
            if(array_key_exists($id, $products)) $products[$id]++;
            else $products[$id] = 1;
            $products = json_encode($products);
            $res = setcookie("cart", $products, $expire, "/");
            return $res;
        }
    }

    public static function delProductOne($id, int $quantity = 1)
    {
        $user = new User();

        if($user->isAuthenticated()) {
            $db = new Database();
            $q = "SELECT quantity FROM incart AS i WHERE i.idUser = ? and i.idProduct = ?";
            $res = $db->queryOne($q, [$user->get("id"), $id]) ?: null;
            if($res == null) return $res;

            $params = ["idUser" => $user->get("id"), "idProduct" => $id];
            if(isset($res["quantity"]) && $res["quantity"] <= 1) {
                $q = "DELETE FROM incart WHERE idUser = :idUser and idProduct = :idProduct";
            } else {
                $q = "UPDATE incart AS i SET i.quantity = i.quantity-:quantity WHERE i.idUser = :idUser and i.idProduct = :idProduct";
                $params["quantity"] = $quantity;
            }
            $res = $db->query($q, $params);
            return $res;
        } else {
            $products = isset($_COOKIE["cart"]) ? json_decode(stripslashes($_COOKIE['cart']), true) : [];
            foreach($products as $cid => $quantity) {
                if($cid == $id) $products[$cid]-=1;
                if($quantity <= 0) unset($products[$id]);
            }
            $expire = time()+60*60*24*30; // 30 jours
            $products = json_encode($products);
            $res = setcookie("cart", $products, $expire, "/");
            return $res;
        }
    }

    public static function mergeCartCookies() 
    {
        $products = isset($_COOKIE["cart"]) ? json_decode(stripslashes($_COOKIE['cart']), true) : [];
        foreach($products as $id => $quantity) {
            Cart::addProductOne($id, $quantity);
        }

        if (isset($_COOKIE["cart"])) {
            unset($_COOKIE["cart"]); 
            setcookie("cart", null, -1, '/'); 
        }
    }
}