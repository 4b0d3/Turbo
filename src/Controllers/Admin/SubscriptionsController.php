<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\Subscriptions;

class SubscriptionsController extends BaseController 
{
    /**** GET ALL SUBSCRITPTIONS ENTITIES ****/
    public function getAll(array $data = [])
    {
        if(!$this->checkAdminAccess()) return;

        $data["subscriptions"] = Subscriptions::getAll();
        $this->display("admin/subscriptions/subscriptions.html.twig", $data);
    }

   
}