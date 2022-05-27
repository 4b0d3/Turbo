<?php 

namespace App\Models;

use App\Database\Database;

class Scooters {
    public static function get(int $id)
    {
        $db = new Database();
        $q = "SELECT * FROM scooters WHERE id = ?";

        return $db->queryOne($q, [$id]) ?: null;;
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

        return $db->query($q, [$id]);
    }

    public static function putInUse(int $id, int $state = 1)
    {
        $db = new Database();
        $q = "UPDATE scooters SET inUse = ? WHERE id = ?";

        return $db->query($q, [$state, $id]);
    }
}