<?php 

namespace App\Controllers;

use App\Models\Scooters;

class ShopController extends BaseController {
    public function get() {
        $this->display("shop.html.twig");
    }
}