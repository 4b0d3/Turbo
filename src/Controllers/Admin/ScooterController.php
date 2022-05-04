<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\Scooters;
class ScooterController extends BaseController 
{
    public function post()
    {
        
    }

    public function get()
    {
        
    }

    public function delete()
    {
    
            $data["scooters"] = Scooters::delete($this->match["params"]["id"]);
            $data["header"]["admin"] = 1;   
            $this->display("Admin/scooters.html.twig", $data);
            
    }
}