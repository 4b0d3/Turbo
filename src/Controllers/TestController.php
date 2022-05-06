<?php 

namespace App\Controllers;

use App\Database\Database;
use App\Entity\User;
use App\Models\Scooters;
use App\Models\Users;

class TestController extends BaseController {
    public function get() {
        $db = new Database();

        // $res = $db->queryAll("SELECT users.*, roles.name as role FROM users LEFT JOIN roles ON users.role = roles.id");
        // dump($res);

        // $user = new User();
        // dump($user);
        // Détruit toutes les variables de session
        $_SESSION = array();

        // Si vous voulez détruire complètement la session, effacez également
        // le cookie de session.
        // Note : cela détruira la session et pas seulement les données de session !
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        // Finalement, on détruit la session.
        session_destroy();
        header("Location:". HOST);
        // $this->display("shop.html.twig");
    }
}