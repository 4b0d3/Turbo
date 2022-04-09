<?php 

include("../vendor/autoload.php");

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

$router = new AltoRouter();
$loader = new FilesystemLoader("../templates");
$twig = new Environment($loader);


$router->map("GET", "/", "home");

$match = $router->match();

if($match !== false) {
    $data = [];
    
    if(file_exists("../src/controllers/" . ucfirst($match["target"]) . ".php")) {
        require_once "../src/controllers/" . ucfirst($match["target"]) . ".php";
        $controllerName = ucfirst($match["target"]);
        $controller = new $controllerName();
        $data = $controller->render();
    }
    
    $twig->display(isset($data["templateName"]) ? $data["templateName"] : "404.html.twig" , $data);

} else {
    $twig->display("404.html.twig", []);
}



