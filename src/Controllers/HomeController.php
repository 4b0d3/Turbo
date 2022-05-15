<?php

namespace App\Controllers;

class HomeController extends BaseController 
{
    public function get() {
        $data = ["title" => "Home | Turbo"];
        $this->display("site/home.html.twig", $data);
    }
}