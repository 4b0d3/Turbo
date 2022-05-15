<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Entity\FormChecker;
use App\Entity\User;
use App\Models\Roles;
use App\Models\Users;

class UsersController extends BaseController 
{
    /**** ALL USERS ACTION ****/
    public function getAll(array $data = [])
    {
            $data["users"] = Users::getAll();
            $this->display("Admin/users.html.twig", $data);
    }

    /**** ONE USER ACTION ****/
    public function getAdd() 
    {
        $data = [];
        $data["roles"] = Roles::getAll();

        $this->display("admin/usersAdd.html.twig", $data);
    }

    public function postAdd()
    {
        $res = Users::add($_POST);
        if($res["status"]) {
            $this->getAll(["boxMsgs" => $res["boxMsgs"]]);
            return;
        }

        $data = [];
        $data["roles"] = Roles::getAll();
        $data = array_merge_recursive($data, $res);

        $this->display("admin/usersAdd.html.twig", $data);
    }

    public function patchOne()
    {
        
    }

    public function delOne()
    {
        
    }

    /**** ROLES ****/
    public function showAllRoles()
    {
        $data = [];
        $data["roles"] = Roles::getAll();
        $this->display("admin/roles.html.twig", $data);
    }
}