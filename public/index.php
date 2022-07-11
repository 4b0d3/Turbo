<?php 

include("../vendor/autoload.php");

use App\Router\Router;

use Symfony\Component\Dotenv\Dotenv;

session_start();

date_default_timezone_set("Europe/Paris");

$dotenv = new Dotenv();
$dotenv->load("../.env");


define('HOST', $_ENV["HOST"]);
define('STYLESHEETS', HOST . "css/");
define('JAVASCRIPTS', HOST . "javascript/");
define('UPLOADS', HOST . "uploads/");
define('VIEWS', dirname(__DIR__) . DIRECTORY_SEPARATOR . "views" . DIRECTORY_SEPARATOR);


$router = new Router();
$route = isset($_REQUEST["route"]) ? "/" . $_REQUEST["route"] : header("Location:" . HOST . "fr/");



$router

    /* ADMIN */
    // scooters                                                                                     
    ->get("/admin/scooters/", "Admin\\Scooters@getAll")
    ->get("/admin/scooters/[i:id]/delete/", "Admin\\Scooter@delete")
    ->get("/admin/scooters/[i:id]/edit/", "Admin\\Scooter@get")
    ->post("/admin/scooters/[i:id]/edit/", "Admin\\Scooter@post")
    // Users
    ->get("/admin/users/", "Admin\\Users@getAll")
    ->get("/admin/users/[i:id]/view/", "Admin\\Users@getView")
    ->get("/admin/users/add/", "Admin\\Users@getAdd")
    ->post("/admin/users/add/", "Admin\\Users@postAdd")
    ->get("/admin/users/[i:id]/edit/", "Admin\\Users@getEdit")
    ->post("/admin/users/[i:id]/edit/", "Admin\\Users@postEdit")
    ->get("/admin/users/[i:id]/delete/", "Admin\\Users@getDel")
    ->post("/admin/users/[i:id]/delete/", "Admin\\Users@postDel")
    ->get("/admin/users/[i:id]/rides/", "Admin\\Users@getRides")
    // Roles
    ->get("/admin/roles/", "Admin\\Users@getAllRoles")
    ->get("/admin/roles/add/", "Admin\\Users@getAddRoles")
    ->post("/admin/roles/add/", "Admin\\Users@postAddRoles")
    ->get("/admin/roles/[i:id]/delete/", "Admin\\Users@postDelRoles")
    // Subscriptions
    ->get("/admin/subscriptions/", "Admin\\Subscriptions@getAll")
    // Products
    ->get("/admin/products/", "Admin\\Products@getAll")
    ->get("/admin/products/add/", "Admin\\Products@getAdd")
    ->post("/admin/products/add/", "Admin\\Products@postAdd")
    ->get("/admin/products/[i:id]/view/", "Admin\\Products@getView")
    ->get("/admin/products/[i:id]/edit/", "Admin\\Products@getEdit")
    ->post("/admin/products/[i:id]/edit/", "Admin\\Products@postEdit")
    ->get("/admin/products/[i:id]/delete/", "Admin\\Products@getDel")
    ->post("/admin/products/[i:id]/delete/", "Admin\\Products@postDel")


    /* AJAX */
    ->get("/ajax/cart/", "Ajax@cart")
    ->get("/ajax/address/", "Ajax@address")

    /* WEBSITE */
    ->get("/[a:lang]/test/", "Site@test")
    ->get("/[a:lang]/disconnect/", "User@disconnect")
    // User profil
    ->get("/[a:lang]/my-account/", "User@showInformations", "myaccount")
    ->post("/[a:lang]/my-account/", "User@editInformations")
    ->get("/[a:lang]/my-account/orders/", "User@showOrders")
    ->get("/[a:lang]/my-account/returns/", "User@showReturns")
    ->get("/[a:lang]/my-account/rides/", "User@showRides")
    ->get("/[a:lang]/my-account/change-password/", "User@showChangePassword")
    ->post("/[a:lang]/my-account/change-password/", "User@editChangePassword")
    ->get("/[a:lang]/my-account/addresses/", "User@getAddresses")
    ->post("/[a:lang]/my-account/addresses/", "User@postAddresses")
    ->post("/[a:lang]/my-account/addresses/delete/", "User@deleteAddresses")
    ->get("/[a:lang]/my-account/subscriptions/", "User@getSubscriptions")
    ->post("/[a:lang]/my-account/subscriptions/", "User@postSubscriptions")
    ->get("/[a:lang]/my-account/notifications/", "User@showNotifications")

    // Login
    ->get("/[a:lang]/login/", "User@getLogin", "login")
    ->post("/[a:lang]/login/", "User@postLogin")
    // Register
    ->get("/[a:lang]/register/", "User@getRegister")
    ->post("/[a:lang]/register/", "User@postRegister")
    
    // Pages
    ->get("/[a:lang]/", "Site@getHome")

    // SHOP
    ->get("/[a:lang]/shop/", "Shop@getAll")
    ->get("/[a:lang]/product/[i:id]/", "Shop@getProduct", "product")
    ->get("/[a:lang]/product/[i:id]/add/", "Shop@addProduct")
    ->get("/[a:lang]/product/[i:id]/delete/", "Shop@deleteProduct")
    ->get("/[a:lang]/cart/", "Shop@getCart")
    ->get("/[a:lang]/choose-shippment/", "Shop@getChooseShippment")
    // SUBSCRIPTION 
    ->get("/[a:lang]/subscriptions/", "Shop@getAllSubcriptions")
    ->post("/[a:lang]/subscriptions/add/", "Shop@addSubscription")

    // API
    ->get("/api/scooter/update/", "API\\Scooter@update")
    ->post("/api/ride/start/", "API\\Scooter@startRide")
    ->post("/api/ride/stop/", "API\\Scooter@stopRide")

    ->run($route);
