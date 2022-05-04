<?php

namespace App\Router;

use \AltoRouter;

class Router {

    public $router;
    
    public function __construct()
    {
        $this->router = new AltoRouter();
    }

    public function get(string $url, string $view, ?string $name = null) :self
    {
        $this->router->map('GET', $url, $view, $name);
        
        return $this;
    }

    public function post(string $url, string $view, ?string $name = null) :self
    {
        $this->router->map('POST', $url, $view, $name);
        
        return $this;
    }

    public function run(?string $route = null) :self 
    {

        $match = $this->router->match($route);

        if($match !== false && isset($match['target'])) {
            $controllerName = "\\App\\Controllers\\" . explode("@", $match['target'])[0] . "Controller";
            $action = explode("@", $match['target'])[1];
            $controller = new $controllerName($match, $this);
            $controller->$action();
        } else {
            $match["error"] = 404;
            $controller = new \App\Controllers\ErrorsController($match);
            $controller->get();
        }

        return $this;
    }

    public function generate(string $routeName) :string
    {
        $basePath = "http://localhost/ESGI/ESGI2/Projet%20Annuel/Projet/public";
        $route = $this->router->generate($routeName);
        return $basePath . $route;
    }
    
}