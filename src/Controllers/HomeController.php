<?php

namespace App\Controllers;

class HomeController extends BaseController {
    public function index() {
        $this->display("home.html.twig", ["title" => "Turbo", "message" => "Sign in"]);
    }
}