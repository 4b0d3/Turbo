<?php

namespace App\Controllers;

use App\Database\Database;
use App\Models\Users;
use App\Models\Roles;

class SiteController extends BaseController 
{
    public function getHome() 
    {
        $data = ["title" => "Home | Turbo"];
        $this->display("site/home.html.twig", $data);
    }
}