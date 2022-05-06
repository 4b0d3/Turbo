<?php 

include("../vendor/autoload.php");

use App\Router\Router;


define('HOST', "/Turbo/public/");
define('STYLESHEETS', HOST . "css/");
define('JAVASCRIPTS', HOST . "javascript/");
define('UPLOADS', HOST . "uploads/");
define('VIEWS', dirname(__DIR__) . DIRECTORY_SEPARATOR . "views" . DIRECTORY_SEPARATOR);


$router = new Router();
$route = isset($_REQUEST["route"]) ? "/" . $_REQUEST["route"] : "/fr/";

session_start();

$router
    ->get("/admin/scooters", "Admin\\Scooters@get", "AdminScooter")
    ->get("/[a:lang]/", "Home@get", "Home")
    ->get("/[a:lang]/login", "Login@get", "Login")
    ->post("/[a:lang]/login", "Login@post")
    ->get("/[a:lang]/register", "Register@get", "Register")
    ->post("/[a:lang]/register", "Register@post")
    ->get("/[a:lang]/shop", "Shop@get")
    ->get("/[a:lang]/user/[i:id]/password", "UserPass@get")
    ->post("/[a:lang]/user/[i:id]/password", "UserPass@post")
    ->get("/disconnect", "Disconnect@get")
    ->run($route);
