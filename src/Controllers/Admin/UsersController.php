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
        if(!$this->checkAdminAccess()) return;

        $data["users"] = Users::getAll();
        $this->display("admin/users/users.html.twig", $data);
    }

    /**** ONE USER ACTION ****/
    public function getAdd(array $data = []) 
    {
        if(!$this->checkAdminAccess()) return;

        $data["roles"] = Roles::getAll();

        $this->display("admin/users/usersAdd.html.twig", $data);
    }

    public function postAdd()
    {
        if(!$this->checkAdminAccess()) return;

        if(isset($_POST["email"])) $_POST["confirmEmail"] = $_POST["email"];
        $res = Users::add($_POST);

        if($res["status"]) {
            $val = isset($res["boxMsgs"][0]) ? implode(";", $res["boxMsgs"][0]) : "Succès;success;L'utilisateur a bien été créé.";
            $redirect = HOST . "admin/users/?boxMsgs=" . $val;
            header("Location:" . $redirect);
            return;
        }

        if(isset($res["checkedFields"])) $data["checkedFields"] = $res["checkedFields"];
        if(isset($res["boxMsgs"])) $data["boxMsgs"] = $res["boxMsgs"];
        if(isset($res["error"])) $data["form"]["error"] = $res["error"];

        $this->getAdd($data);
    }

    public function getEdit(array $data = [])
    {
        if(!$this->checkAdminAccess()) return;

        $userId = $this->match["params"]["id"] ?? null;
        $data["roles"] = Roles::getAll();
        $data["userInfo"] = Users::get($userId);

        if(empty($userId) || intval($userId) <= 0 || !$data["userInfo"]) {
            header("Location:" . HOST . "admin/users/?boxMsgs=Erreur;error;Utilisateur non trouvé.");
            return;
        }

        $this->display("admin/users/usersEdit.html.twig", $data);
    }

    public function postEdit()
    {
        if(!$this->checkAdminAccess()) return;

        $userId = $this->match["params"]["id"] ?? null;
        $user = Users::get($userId);

        if(empty($userId) || intval($userId) <= 0 || !$user) {
            header("Location:" . HOST . "admin/users/?boxMsgs=Erreur;error;Utilisateur non trouvé.");
            return;
        }

        $_POST["id"] = $userId;
        Users::updateOneById($_POST);
        $this->getEdit();
    }

    public function getDel(array $data = [])
    {
        if(!$this->checkAdminAccess()) return;

        $userId = $this->match["params"]["id"] ?? null;
        $data["userInfo"] = Users::get($userId);

        if(empty($userId) || intval($userId) <= 0 || !$data["userInfo"]) {
            header("Location:" . HOST . "admin/users/?boxMsgs=Erreur;error;Utilisateur non trouvé.");
            return;
        }

        $this->display("admin/users/usersDel.html.twig", $data);
        
    }

    public function postDel()
    {
        if(!$this->checkAdminAccess()) return;

        $userId = $this->match["params"]["id"] ?? null;
        $data["userInfo"] = Users::get($userId);

        if(empty($userId) || intval($userId) <= 0 || !$data["userInfo"]) {
            header("Location:" . HOST . "admin/users/?boxMsgs=Erreur;error;Utilisateur non trouvé.");
            return;
        }

        $res = Users::delete($userId);

        if($res) {
            $val = isset($res["boxMsgs"][0]) ? implode(";", $res["boxMsgs"][0]) : "Succès;success;L'utilisateur a bien été supprimé.";
            $redirect = HOST . "admin/users/?boxMsgs=" . $val;
            header("Location:" . $redirect);
            return;
        }

        if(isset($res["boxMsgs"])) $data["boxMsgs"] = $res["boxMsgs"];
        else $data["boxMsgs"] = [["status" => "Erreur", "class" => "error", "description" => "L'utilisateur n'a pas pu être supprimé."]];

        $this->getDel($data);
    }

    public function getView()
    {
        if(!$this->checkAdminAccess()) return;

        $userId = $this->match["params"]["id"] ?? null;
        $data["userInfo"] = Users::get($userId);

        if(empty($userId) || intval($userId) <= 0 || !$data["userInfo"]) {
            header("Location:" . HOST . "admin/users/?boxMsgs=Erreur;error;Utilisateur non trouvé.");
            return;
        }

        $this->display("admin/users/usersView.html.twig", $data);
    }

    /**** ROLES ****/
    public function getAllRoles(array $data = [])
    {
        if(!$this->checkAdminAccess()) return;

        $data["roles"] = Roles::getAll();
        $this->display("admin/roles.html.twig", $data);
    }

    public function getAddRoles(array $data = [])
    {
        if(!$this->checkAdminAccess()) return;

        $data["roles"] = Roles::getAll();

        $this->display("admin/rolesAdd.html.twig", $data);
    }

    public function postAddRoles()
    {
        if(!$this->checkAdminAccess()) return;

        $res = Roles::add($_POST);
        if($res["status"]) {
            $val = isset($res["boxMsgs"][0]) ? implode(";", $res["boxMsgs"][0]) : "Succès;success;Le rôle a bien été créé.";
            $redirect = HOST . "admin/roles/?boxMsgs=" . $val;
            header("Location:" . $redirect);
            return;
        }

        if(isset($res["checkedFields"])) $data["checkedFields"] = $res["checkedFields"];
        if(isset($res["boxMsgs"])) $data["boxMsgs"] = $res["boxMsgs"];
        if(isset($res["error"])) $data["form"]["error"] = $res["error"];

        $this->getAddRoles($data);   
    }

    public function postDelRoles()
    {
        if(!$this->checkAdminAccess()) return;

        $roleId = $this->match["params"]["id"] ?? null;
        $data["roleInfo"] = Roles::get($roleId);

        if(empty($roleId) || intval($roleId) <= 0 || !$data["roleInfo"]) {
            header("Location:" . HOST . "admin/roles/?boxMsgs=Erreur;error;Rôle non trouvé suppression impossible.");
            return;
        }

        $res = Roles::delete($roleId);

        if($res) {
            $val = isset($res["boxMsgs"][0]) ? implode(";", $res["boxMsgs"][0]) : "Succès;success;Le rôle a bien été supprimé.";
            $redirect = HOST . "admin/roles/?boxMsgs=" . $val;
            header("Location:" . $redirect);
            return;
        }

        if(isset($res["boxMsgs"])) $data["boxMsgs"] = $res["boxMsgs"];
        else $data["boxMsgs"] = [["status" => "Erreur", "class" => "error", "description" => "Le rôle n'a pas pu être supprimé."]];

        $this->getDel($data);
    }
}