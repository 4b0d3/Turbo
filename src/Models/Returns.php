<?php

namespace App\Models;

use App\Database\Database;

class Returns {
    
    public static function get(int $id)
    {
        if($id == null || $id <= 0) return null;

        $db = new Database();
        $q = "SELECT * FROM scooters WHERE id = ?";

        $res = $db->queryOne($q, [$id]) ?: null;
        return $res;
    }

    public static function allReturns(){
        $db = new Database();
        
        $q = "SELECT c.*, a.address FROM commands as c INNER JOIN addresses as a where c.isReturn = 1 AND c.idAddress = a.id" ;
        
        $res = null;
        
        $res = $db->queryAll($q);


        $res = $db->queryAll($q) ?: null;

        return $res;
    }

    public static function getOrder(int $id)
    {
        if($id == null || $id <= 0) return null;

        $db = new Database();
        $q = "SELECT * FROM commands WHERE id = ?";

        return $db->queryOne($q, [$id]);
    }

    public static function valideReturn($commandId)
    {
        $db = new Database();
        $q = "UPDATE commands SET valideReturn = ?, isReturn = ? WHERE id = ?";

        return $db->query($q, [1, 0, $commandId]);
    }
}