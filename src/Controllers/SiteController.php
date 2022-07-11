<?php

namespace App\Controllers;

use App\Models\Cart;
use App\Models\Products;

class SiteController extends BaseController 
{
    public function getHome() 
    {
        $data = ["title" => "Home | Turbo"];
        $data["cart"]["products"] = Cart::getAllProducts();
        $data["vitrine"]["products"] = Products::getAll(0, 3);
        $this->display("site/home.html.twig", $data);
    }

    public function test(array $data = [])
    {

        $cart = [
            ["id" => 1, "quantity" => 2]
        ];

        dump($cart);
        dump(json_encode($cart));
        dump(json_decode(json_encode($cart), true));
        dump(json_encode(json_decode(json_encode($cart))));
        // $o = (new \stdClass());
        // $o->quantity = "ok";
        // dump($o);



        $this->display("test.html.twig", $data);
    }
}