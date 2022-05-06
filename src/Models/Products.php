<?php 

namespace App\Models;

use App\Database\Database;

class Products {
    public static function get(int $id)
    {
        $db = new Database();
        $q = "SELECT * FROM products WHERE id = ?";

        $res = [];
        if($id != null && $id > 0) {
            $res = $db->queryOne($q, [$id]);
        }

        return $res;
    }

    public static function getAll(int $start = null, int $total = null) :array
    {
        $db = new Database();
        $q = "SELECT * FROM products";

        $res = [];
        if(!($start == null || $start < 0 || $total == null || $total < 0 )) {
            $q = $q . " LIMIT " . $start . ", " . $total; 
            $res = $db->queryAll($q);
        } else {
            $res = $db->queryAll($q);
        }

        return $res;
    }
}