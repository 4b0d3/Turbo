<?php 

namespace App\Entity;

use App\Database\Database;
use App\Models\Users;

class User {

    protected $authenticated;

    protected $db;

    protected $user;

    public function __construct()
    {
        $this->db = new Database();
        $this->initialize();
    }

    public function initialize() 
    {
        $this->authenticated = false;
        $this->user = null;
        
        if(isset($_SESSION["id"]) && !empty($_SESSION["id"])) {
            $this->user = Users::get($_SESSION["id"]) ?: null;
            $this->checkAuth();   
        }

        /**
         * TODO
         * Identification par token ? maybe
         */
    }

    protected function checkAuth() :self
    {
        if(isset($_SESSION["id"]) && !empty($_SESSION["id"]) && $this->user != null) {
            $this->authenticated = true;
        } else {
            $this->authenticated = false;
        }
        
        return $this;
    }

    public function get(string $attr)
    {
        if(isset($this->user[$attr]) && !empty($this->user[$attr])) {
            return $this->user[$attr];
        }

        return null;
    }


    public function hasRole(string $role) :bool
    {
        if($this->get("role") == $role) {
            return true;
        } else {
            return false;
        }
    }

    public function isAuthenticated() {
        return $this->authenticated;
    }

    public function isAnonymous() {
        return !$this->authenticated;
    }
}