<?php

use App\Models\Cart;
use App\Models\Products;

include("../../../vendor/autoload.php");
session_start();

$action = null;
if(isset($_GET["action"]) && !empty($_GET["action"]) && in_array($_GET["action"], ["add", "del"]) ) {
    $action = $_GET["action"];
}

$product = null;
if(isset($_GET["productId"]) && !empty($_GET["productId"])) {
    $productId = intval($_GET["productId"]) <= 0 ? null : intval($_GET["productId"]);
    if($productId != null) {
        $product = Products::get($productId) ?: null ;
    }
}

if($action == null || $product == null) {
    exit;
}

if($action == "add") {
    $cart = Cart::add($_GET["productId"]);
}

if($action == "del") {
    $cart = Cart::del($_GET["productId"]);
}