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
            if(method_exists($controllerName, $action)){
                $controller = new $controllerName($match, $this);
                $controller->$action();
                return $this;
            }
        }
        
        $match = [];
        $match["error"] = 404;
        $controller = new \App\Controllers\ErrorsController($match);
        $controller->get();

        return $this;
    }

    public function generate(string $routeName, array $params = []) :string
    {
        $basePath = defined("HOST") ? substr(HOST, 0, -1) : "";
        $route = $this->router->generate($routeName, $params);
        return $basePath . $route;
    }
    
}