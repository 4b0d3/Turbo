<?php 

namespace App\Controllers;

use App\Database\Database;

class TestController extends BaseController 
{
    public function get() {
        $this->display("test.html.twig");
    }
}