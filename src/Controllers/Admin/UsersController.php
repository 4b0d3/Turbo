<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\Users;

class UsersController extends BaseController 
{
    public function get()
    {
            $data["users"] = Users::getAll();
            $data["header"]["admin"] = 1;
            $this->display("Admin/users.html.twig", $data);
    }
}