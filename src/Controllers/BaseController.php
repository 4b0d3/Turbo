<?php 

namespace App\Controllers;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use App\Database\Database;
use App\Entity\User;
use App\Router\Router;

class BaseController {

    protected $match;

    protected $router;

    protected $user;

    protected $FSLoader;
    
    protected $twig;

    public function __construct(array $match = null, Router $router = null)
    {
        $this->match = $match;
        $this->router = $router;
        $this->user = new User();
        $this->FSLoader = new FilesystemLoader(VIEWS);
        $this->twig = new Environment($this->FSLoader, [
            "debug" => true
        ]);
        $this->twig->addExtension(new \Twig\Extension\DebugExtension());
    }

    public function display(string $template, array $data = []) 
    {
        $data["HOST"] = HOST;
        $data["BASEURL"] = isset($this->match["params"]["lang"]) ? HOST . $this->match["params"]["lang"] . "/" : HOST;
        $data["HERE"] = isset($_REQUEST["route"]) ? $data["HOST"] . $_REQUEST["route"] : HOST;
        $data["STYLESHEETS"] = defined("STYLESHEETS") ? STYLESHEETS : "../public/css";
        $data["UPLOADS"] = defined("UPLOADS") ? UPLOADS : "../public/uploads";
        $data["user"]["role"] = $this->user->get("role");
        dump($data);
        $this->twig->display($template, $data);
    }

}