<?php 

namespace App\Controllers;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use App\Database\Database;

class BaseController {

    protected $match;

    protected $FSLoader;
    
    protected $twig;

    protected $db;


    public function __construct(?array $match)
    {
        $this->match = $match;
        $this->FSLoader = new FilesystemLoader(VIEWS);
        $this->twig = new Environment($this->FSLoader);
        $this->db = new Database();
    }

    public function display(string $template, array $data = []) 
    {
        $data["STYLESHEETS"] = defined("STYLESHEETS") ? STYLESHEETS : "../public/css";
        $data["UPLOADS"] = defined("UPLOADS") ? UPLOADS : "../public/uploads";
        $this->twig->display($template, $data);
    }

}