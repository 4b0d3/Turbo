<?php 

namespace App\Models;

use App\Database\Database;
use App\Entity\User;

class Cart {
    public static function getId() {
        $user = new User();
        $db = new Database();

        if($user->isAuthenticated()) {
            $q = "SELECT id FROM carts WHERE carts.userId = ?";
            $res = $db->queryOne($q, [$user->get("id")]) ?: null;
            if($res != null) {
                return $res["id"];
            } else {
                return null;
            }
        } else {
            return null;   
        }
    }

    public static function get() {
        $user = new User();
        $db = new Database();

        if($user->isAuthenticated()) {
            $q = "SELECT * FROM carts INNER JOIN incart ON carts.id = incart.cartId INNER JOIN products ON incart.productId = products.id WHERE carts.userId = ?";
            $res = $db->queryAll($q, [$user->get("id")]);
            return $res;
        } else {
            return null;   
        }
    }

    public static function add(int $productId, int $quantity = 1) :bool
    {
        $cart = Cart::get();
        $user = new User();
        $db = new Database();
        $res = false;

        if($cart != null || (is_array($cart) && empty($cart))) {
            if(!empty($cart) && in_array($productId, array_column($cart, "id"))) {
                if($user->isAuthenticated()) {
                    $attrs = [$quantity, $user->get("id"), $productId];
                    $res = $db->query("UPDATE incart INNER JOIN carts ON incart.cartId = carts.id SET incart.quantity = incart.quantity+? WHERE carts.userId = ? AND incart.productId = ?", $attrs);
                }
            } else {
                if($user->isAuthenticated()) {
                    $attrs = [Cart::getId(), $productId, $quantity];
                    $res = $db->query("INSERT INTO incart(cartId, productId, quantity) VALUES (?,?,?)", $attrs);
                }
            }
        }

        return $res;
    }

    public static function del(int $productId, int $quantity = -1) :bool
    {
        $cart = Cart::get();
        $user = new User();
        $db = new Database();
        $res = false;

        if($cart != null) {
            $attrs = [$user->get("id"), $productId];
            $res = $db->queryOne("SELECT incart.quantity FROM incart INNER JOIN carts ON incart.cartId = carts.id WHERE carts.userId = ? AND incart.productId = ?", $attrs);
            if($res["quantity"] > 1) {
                if($user->isAuthenticated()) {
                    $attrs = [$quantity, $user->get("id"), $productId];
                    $res = $db->query("UPDATE incart INNER JOIN carts ON incart.cartId = carts.id SET incart.quantity = incart.quantity+? WHERE carts.userId = ? AND incart.productId = ?", $attrs);
                }
            } else {
                if($user->isAuthenticated()) {
                    $attrs = [$user->get("id"), $productId];
                    $res = $db->query("DELETE P FROM incart P INNER JOIN carts ON P.cartId = carts.id WHERE carts.userId = ? AND P.productId = ?", $attrs);
                }
            }
        }

        return $res;
    }
}