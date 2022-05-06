<?php

include("../../../vendor/autoload.php");
session_start();

use App\Entity\User;
use App\Models\Cart;

$user = new User();

echo json_encode(Cart::get());
exit;

// $userId = null;
if(isset($_GET["userId"]) && !empty($_GET["userId"])) {
    $userId = intval($_GET["userId"]) <= 0 ? null : intval($_GET["userId"]); 
}