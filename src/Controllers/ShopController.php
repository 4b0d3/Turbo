<?php 

namespace App\Controllers;

use App\Models\Products;
use App\Models\Scooters;

class ShopController extends BaseController 
{
    public function get() {
        $page = 0;
        $perPage = 10;

        if(isset($_GET["page"]) && !empty($_GET["page"])) {
            $page = intval($_GET["page"]) < 0 ? 0 : intval($_GET["page"]);
        }

        $products = Products::getAll($page, $perPage);
        $data["products"] = $products;


        $this->display("shop.html.twig", $data);
    }
}