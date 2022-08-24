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
    ->get("/admin/subscriptions/add/", "Admin\\Subscriptions@getAdd")
    ->post("/admin/subscriptions/add/", "Admin\\Subscriptions@postAdd")
    ->get("/admin/subscriptions/[i:id]/view/", "Admin\\Subscriptions@getView")
    ->get("/admin/subscriptions/[i:id]/delete/", "Admin\\Subscriptions@getDel")
    ->post("/admin/subscriptions/[i:id]/delete/", "Admin\\Subscriptions@postDel")
    ->get("/admin/subscriptions/[i:id]/edit/", "Admin\\Subscriptions@getEdit")
    ->post("/admin/subscriptions/[i:id]/edit/", "Admin\\Subscriptions@postEdit")
    // Products
    ->get("/admin/products/", "Admin\\Products@getAll")
    ->get("/admin/products/add/", "Admin\\Products@getAdd")
    ->post("/admin/products/add/", "Admin\\Products@postAdd")
    ->get("/admin/products/[i:id]/view/", "Admin\\Products@getView")
    ->get("/admin/products/[i:id]/edit/", "Admin\\Products@getEdit")
    ->post("/admin/products/[i:id]/edit/", "Admin\\Products@postEdit")
    ->get("/admin/products/[i:id]/delete/", "Admin\\Products@getDel")
    ->post("/admin/products/[i:id]/delete/", "Admin\\Products@postDel")
    // Partners
    ->get("/admin/partners/", "Admin\\Partners@getAll")
    ->get("/admin/partners/[i:id]/view/", "Admin\\Partners@getView")
    ->get("/admin/partners/[i:id]/edit/", "Admin\\Partners@getEdit")
    ->post("/admin/partners/[i:id]/edit/", "Admin\\Partners@postEdit")
    ->get("/admin/partners/[i:id]/delete/", "Admin\\Partners@getDel")
    ->post("/admin/partners/[i:id]/delete/", "Admin\\Partners@postDel")


    //Invoices
    ->get("/admin/invoices/", "Admin\\Invoices@getAll")
    ->get("/admin/invoices/[i:id]/view/", "Admin\\Invoices@getView")
    ->get("/admin/invoices/[i:id]/edit/", "Admin\\Invoices@getEdit")
    ->post("/admin/invoices/[i:id]/edit/", "Admin\\Invoices@postEdit")
    ->get("/admin/invoices/[i:id]/delete/", "Admin\\Invoices@getDel")
    ->post("/admin/invoices/[i:id]/delete/", "Admin\\Invoices@postDel")
    


    /* AJAX */
    ->get("/ajax/cart/", "Ajax@cart")
    ->get("/ajax/address/", "Ajax@address")



    // API
    ->get("/api/scooter/update/", "API\\Scooter@update")
    ->post("/api/ride/start/", "API\\Scooter@startRide")
    ->post("/api/ride/stop/", "API\\Scooter@stopRide")
    ->get("/api/scooter/get/all/available/", "API\\Scooter@getAllDisponibles")
    ->post("/api/login/", "API\\Auth@login")
    ->post("/api/user/token/verif/", "API\\Auth@isConnectedByToken")

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

    ->get("/[a:lang]/my-account/invoices/", "User@getInvoices")
    ->post("/[a:lang]/my-account/invoices/", "User@postInvoices")

    ->get("/[a:lang]/my-account/partners/", "User@getPartner")
    ->post("/[a:lang]/my-account/partners/", "User@postPartner")


    // Login
    ->get("/[a:lang]/login/", "User@getLogin", "login")
    ->post("/[a:lang]/login/", "User@postLogin")
    // Register
    ->get("/[a:lang]/register/", "User@getRegister")
    ->post("/[a:lang]/register/", "User@postRegister")

    // Verification
    ->get("/verify/[*:email]/[a:token]/", "User@getVerification")

    
    
    // Pages
    ->get("/[a:lang]/", "Site@getHome")

    // SHOP
    ->get("/[a:lang]/shop/", "Shop@getAll")
    ->get("/[a:lang]/product/[i:id]/", "Shop@getProduct", "product")
    ->get("/[a:lang]/product/[i:id]/add/", "Shop@addProduct")
    ->get("/[a:lang]/product/[i:id]/delete/", "Shop@deleteProduct")
    ->get("/[a:lang]/cart/", "Shop@getCart")
    ->get("/[a:lang]/choose-shippment/", "Shop@getChooseShippment")
    ->get("/[a:lang]/command/pay/", "Shop@getPay")
    ->get("/[a:lang]/command/success/", "Shop@getSuccess")
    // SUBSCRIPTION 
    ->get("/[a:lang]/subscriptions/", "Shop@getAllSubcriptions")
    ->post("/[a:lang]/subscriptions/add/", "Shop@addSubscription")

    // Partners
    ->get("/[a:lang]/partners/", "Shop@getAllPartners")
    ->post("/[a:lang]/partners/add/","Shop@addPartner")

    //Juicers
    ->get("/juicers/scooters/", "User@getAllJuicer")
    ->get("/juicers/scooters/[i:id]/charged/", "User@getCharged")
    ->post("/juicers/scooters/[i:id]/charged/", "User@postCharged")

    ->run($route);
