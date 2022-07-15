<?php 

namespace App\Models;

use App\Database\Database;

class Commands {
    public static function get(int $id)
    {
        if($id == null || $id <= 0) return null;

        $db = new Database();
        $q = "SELECT * FROM commands WHERE id = ?";

        $res = $db->queryOne($q, [$id]) ?: null;

        return $res;
    }

    public static function getAll($idUser, int $start = null, int $total = null)
    {
        $db = new Database();
        $q = "SELECT * FROM commands WHERE idUser = ?";

        $res = null;
        if(!($start == null || $start < 0 || $total == null || $total < 0 )) {
            $q = $q . " LIMIT " . $start . ", " . $total; 
            $res = $db->queryAll($q, [$idUser]);
        }

        $res = $db->queryAll($q, [$idUser]) ?: null;

        return $res;
    }

    public static function getAllNotReturned($idUser, int $start = null, int $total = null)
    {
        $db = new Database();
        $q = "SELECT * FROM commands WHERE idUser = ? AND isReturn = 0";

        $res = null;
        if(!($start == null || $start < 0 || $total == null || $total < 0 )) {
            $q = $q . " LIMIT " . $start . ", " . $total; 
            $res = $db->queryAll($q, [$idUser]);
        }

        $res = $db->queryAll($q, [$idUser]) ?: null;

        return $res;
    }

    public static function getAllReturned($idUser, int $start = null, int $total = null)
    {
        $db = new Database();
        $q = "SELECT * FROM commands WHERE idUser = ? AND isReturn = 1";

        $res = null;
        if(!($start == null || $start < 0 || $total == null || $total < 0 )) {
            $q = $q . " LIMIT " . $start . ", " . $total; 
            $res = $db->queryAll($q, [$idUser]);
        }

        $res = $db->queryAll($q, [$idUser]) ?: null;

        return $res;
    }

    public static function add($commandInfo)
    {     
        $db = new Database();

        try {
            $q = "INSERT INTO commands(idUser, idAddress, total, products) VALUES(:idUser, :idAddress, :total, :products)";
            $db->query($q, $commandInfo);
            $data["boxMsgs"] = [["status" => "Succès", "class" => "success", "description" => "La commande a bien été passée."]];
            $data["status"] = true;
        } catch (\Exception $e) {
            $data["boxMsgs"] = [["status" => "Erreur", "class" => "error", "description" => $e->getMessage()]];
            $data["status"] = false;
        }

        return $data;  
    }

}