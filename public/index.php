<?php 

include("../vendor/autoload.php");

use App\Router\Router;


define('HOST', "/public/");
define('STYLESHEETS', HOST . "css/");
define('JAVASCRIPTS', HOST . "javascript/");
define('UPLOADS', HOST . "uploads/");
define('VIEWS', dirname(__DIR__) . DIRECTORY_SEPARATOR . "views" . DIRECTORY_SEPARATOR);


$router = new Router();
$route = isset($_REQUEST["route"]) ? "/" . $_REQUEST["route"] : "/fr/";
//dump($_REQUEST);
session_start();
$_SESSION["id"] = 1;

$router
    ->get("/admin/scooters", "Admin\\Scooters@get", "AdminScooter")
    ->get("/admin/users", "Admin\\Users@get", "AdminUser")
    ->get("/admin/scooters/[i:id]/delete", "Admin\\Scooter@delete")
    // ->get("/admin/scooters/[i:id]/edit", "Admin\\Scooter@get")
    // ->post("/admin/scooters/[i:id]/edit", "Admin\\Scooter@post")
    ->get("/admin/users/[i:id]/delete", "Admin\\Users@delete")

    ->get("/[a:lang]/", "Home@get", "Home")
    ->get("/[a:lang]/login  ", "Login@get", "Login")
    ->post("/[a:lang]/login", "Login@post")
    ->get("/[a:lang]/register", "Register@get", "Register")
    ->post("/[a:lang]/register", "Register@post")
    ->get("/[a:lang]/shop", "Shop@get", "Shop")
    ->get("/test", "Test@get")
    ->run($route);
