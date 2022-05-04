<?php 

namespace App\Models;

use App\Database\Database;

class Scooters {
    public static function get(int $id) :array
    {
        $db = new Database();
        $q = "SELECT * FROM scooters WHERE id = ?";

        $res = [];
        if($id != null && $id > 0) {
            $res = $db->queryOne($q, [$id]);
        }

        return $res;
    }

    public static function getAll(int $start = null, int $total = null) :array
    {
        $db = new Database();
        $q = "SELECT * FROM scooters";

        $res = [];
        if(!($start == null || $start < 0 || $total == null || $total < 0 )) {
            $q = $q . " LIMIT " . $start . ", " . $total; 
            $res = $db->queryAll($q);
        } else {
            $res = $db->queryAll($q);
        }

        return $res;
    }

    public static function delete(int $id) :bool
    {
        $db = new Database();
        $q = "DELETE FROM scooters WHERE id = ?";

        $res = false;
        if($id != null && $id > 0) {
            $res = $db->query($q, [$id]);
        }

        return $res;
    }
    
}