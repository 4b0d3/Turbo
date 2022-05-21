<?php 

include("../vendor/autoload.php");

use App\Router\Router;


// TODO CHANGE HOST UNTIL IMPLEMENTATIONS OF DOTENV
define('HOST', "/ESGI/ESGI2/Projet Annuelp/Turbo/public/");
define('STYLESHEETS', HOST . "css/");
define('JAVASCRIPTS', HOST . "javascript/");
define('UPLOADS', HOST . "uploads/");
define('VIEWS', dirname(__DIR__) . DIRECTORY_SEPARATOR . "views" . DIRECTORY_SEPARATOR);


$router = new Router();
$route = isset($_REQUEST["route"]) ? "/" . $_REQUEST["route"] : header("Location:" . HOST . "fr/");

session_start();

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
    // Roles
    ->get("/admin/roles/", "Admin\\Users@getAllRoles")
    ->get("/admin/roles/add/", "Admin\\Users@getAddRoles")
    ->post("/admin/roles/add/", "Admin\\Users@postAddRoles")
    ->get("/admin/roles/[i:id]/delete/", "Admin\\Users@postDelRoles")


    /* WEBSITE */
    ->get("/[a:lang]/disconnect/", "User@disconnect")
    // User profil
    ->get("/[a:lang]/my-account/", "User@showInformations")
    ->post("/[a:lang]/my-account/", "User@editInformations")
    ->get("/[a:lang]/my-account/orders/", "User@showOrders")
    ->get("/[a:lang]/my-account/returns/", "User@showReturns")
    ->get("/[a:lang]/my-account/rides/", "User@showRides")
    ->get("/[a:lang]/my-account/change-password/", "User@showChangePassword")
    ->post("/[a:lang]/my-account/change-password/", "User@editChangePassword")
    ->get("/[a:lang]/my-account/addresses/", "User@showAddresses")
    ->get("/[a:lang]/my-account/payment-methods/", "User@showPaymentMethods")
    ->get("/[a:lang]/my-account/notifications/", "User@showNotifications")


    // Login
    ->get("/[a:lang]/login/", "Site@getLogin", "login")
    ->post("/[a:lang]/login/", "Site@postLogin")
    // Register
    ->get("/[a:lang]/register/", "Site@getRegister")
    ->post("/[a:lang]/register/", "Site@postRegister")
    
    // Pages
    ->get("/[a:lang]/", "Site@getHome")
    ->get("/[a:lang]/shop", "Shop@get")

    ->run($route);
