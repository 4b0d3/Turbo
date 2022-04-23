<?php 

include("../vendor/autoload.php");

use App\Router\Router;

define('STYLESHEETS', ".." . DIRECTORY_SEPARATOR . "public" . DIRECTORY_SEPARATOR . "css" . DIRECTORY_SEPARATOR);
define('JAVASCRIPTS', __DIR__ . DIRECTORY_SEPARATOR . "js" . DIRECTORY_SEPARATOR);
define('UPLOADS', ".." . DIRECTORY_SEPARATOR . "public" . DIRECTORY_SEPARATOR . "uploads" . DIRECTORY_SEPARATOR);
define('VIEWS', dirname(__DIR__) . DIRECTORY_SEPARATOR . "templates" . DIRECTORY_SEPARATOR);


$router = new Router();
$route = isset($_REQUEST["route"]) ? "/" . $_REQUEST["route"] : "/";


$router
    ->get("/", "Home@index")
    ->get("/Shop", "Shop@index")
    ->get("/login", "Login@index")
    ->get("/test", "Render@index", "home.html.twig")
    ->run($route);




