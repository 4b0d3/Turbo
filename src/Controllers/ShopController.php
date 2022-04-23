<?php 

namespace App\Controllers;

use App\Models\Scooters;

class ShopController extends BaseController {
    public function index() {
        $this->display("shop.html.twig");
    }
}