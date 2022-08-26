<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\Rides;


class RidesController extends BaseController 
{
    /**** GET ALL INVOICES ENTITIES ****/
    public function getAll(array $data = [])
    {
        if(!$this->checkAdminAccess()) return;

        $data["rides"] = Rides::getAll();
        $this->display("admin/rides/rides.html.twig", $data);
    }
   
} 