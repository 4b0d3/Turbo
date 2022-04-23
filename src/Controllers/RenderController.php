<?php

namespace App\Controllers;

class RenderController extends BaseController {
    public function index() {
        $this->display($this->match["name"]);
    }
}