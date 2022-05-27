<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\Scooters;

class ScootersController extends BaseController 
{
    public function getAll()
    {
            $data["scooters"] = Scooters::getAll();
            $data["header"]["admin"] = 1;
            $this->display("admin/scooters/scooters.html.twig", $data);
    }

}