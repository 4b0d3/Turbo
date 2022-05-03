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

        $this->display("shop.html.twig");
    }
}