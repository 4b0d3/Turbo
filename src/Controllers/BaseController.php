<?php 

namespace App\Controllers;

use Stichoza\GoogleTranslate\GoogleTranslate;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use App\Entity\User;
use App\Models\Cart;
use App\Router\Router;
use Twig\TwigFilter;

class BaseController 
{

    protected $match;

    public $router;

    protected $user;

    protected $FSLoader;
    
    protected $twig;

    protected $urls;

    protected $lang;

    public function __construct(array $match = null, Router $router = null)
    {
        $this->match = $match;
        $this->router = $router;
        $this->user = new User(); 
        $this->configureLang();
        $this->configureTwig();

        $this->urls["BASEURL"] = isset($this->match["params"]["lang"]) ? HOST . $this->match["params"]["lang"] . "/" : HOST ;
        $this->urls["HERE"] = isset($_REQUEST["route"]) ? HOST . $_REQUEST["route"] : HOST ;
    }

    public function display(string $template, array $data = []) 
    {
        $data["HOST"] = HOST;
        $data["BASEURL"] = $this->urls["BASEURL"];
        $data["HERE"] = $this->urls["HERE"];
        $data["STYLESHEETS"] = defined("STYLESHEETS") ? STYLESHEETS : "../public/css";
        $data["UPLOADS"] = defined("UPLOADS") ? UPLOADS : "../public/uploads";
        $data["user"] = $this->getUserInfos();
        !empty($this->getBoxMsgs()) ? $data["boxMsgs"] = $this->getBoxMsgs() : "" ;

        $_ENV["DEBUG"] == "true" ? dump($data) : "";
        $this->twig->display($template, $data);
    }

    public function getUserInfos() 
    {
        $user = [
            "id" => $this->user->get("id"), 
            "role" => $this->user->get("role"), 
            "authenticated" => $this->user->isAuthenticated(),
            "firstName" => $this->user->get("firstName"), 
            "name" => $this->user->get("name"),
            "email" => $this->user->get("email")
        ];

        return $user;
    }

    public function getBoxMsgs() 
    {
        if(!isset($_GET["boxMsgs"]) || empty($_GET["boxMsgs"])) {
            return null;
        }

        $boxMsgs = explode(";", $_GET["boxMsgs"]);
        $res = [];

        for($i=0; $i<count($boxMsgs); $i+=3) {
            if(!isset($boxMsgs[$i]) || !isset($boxMsgs[$i+1]) || !isset($boxMsgs[$i+2])) continue;
            $res[] = ["status" => $boxMsgs[$i], "class" => $boxMsgs[$i+1] , "description" => $boxMsgs[$i+2]];
        }

        return empty($res) ? null : $res;
    }

    public function checkAdminAccess() 
    {
        if(!$this->user->hasRole("admin"))  {
            (new \App\Controllers\ErrorsController(["error" => 403]))->get();
            return false;
        }
        return true;
    }

    public function checkjuicerAccess() 
    {
        if(!$this->user->hasRole("juicer"))  {
            (new \App\Controllers\ErrorsController(["error" => 403]))->get();
            return false;
        }
        return true;
    }

    public function configureLang()
    {
        $acceptedLang = ["fr", "en", "de", "pl"];
        if(isset($this->match["params"]["lang"]) && !empty($this->match["params"]["lang"]) && in_array($this->match["params"]["lang"], $acceptedLang)) {
            $this->lang = $this->match["params"]["lang"];
        } else {
            $this->match["params"]["lang"] = "fr";
            $this->lang = "fr";
        }
    }

    public function configureTwig()
    {
        $this->FSLoader = new FilesystemLoader(VIEWS);
        $this->twig = new Environment($this->FSLoader, [
            "debug" => $_ENV["DEBUG"] == "true" ? true : false,
        ]);
        $this->twig->addExtension(new \Twig\Extension\DebugExtension());
        $this->twig->addFilter(new TwigFilter("trans", function ($value) {
            if($this->lang != "fr") {
                $tr = new GoogleTranslate($this->lang);
                $tr->setUrl('http://translate.google.cn/translate_a/single'); 
                $tr->setSource('fr');
                return $tr->translate($value);
            }
            return $value;
        }));
    }

}