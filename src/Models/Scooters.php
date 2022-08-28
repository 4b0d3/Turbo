<?php 

namespace App\Models;

use App\Database\Database;

class Scooters {
    public static function get(int $id)
    {
        $db = new Database();
        $q = "SELECT * FROM scooters WHERE id = ?";

        $res = $db->queryOne($q, [$id]) ?: null;;
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

    public static function getAllDisponibles(int $start = null, int $total = null) :array
    {
        $db = new Database();
        $q = "SELECT * FROM scooters WHERE inUse=0 AND battery>=10";

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

    public static function add(array $scooter){
        $db = new Database();
        $q = "INSERT INTO scooters(battery, status) VALUES(:battery, :status)";

        return $db->query($q, $scooter);
    }

    public static function updateOneById(array $infos)
    {
        $db = new Database();
        $q = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = N'scooters' AND TABLE_SCHEMA = ?";
        $acceptedFields = array_column($db->queryAll($q, [$_ENV["DB_NAME"]]), "COLUMN_NAME");

        $scooter = isset($infos["id"]) ? Scooters::get($infos["id"]) : null;
        if($scooter == null) return false;

        $set = [];
        $attrs["id"] = $infos["id"];
        foreach($infos as $key => $value) {
            if(!in_array($key, $acceptedFields) || $value == $scooter[$key]) {
                continue;
            }

            $attrs[$key] = $value;
            $set[] = "$key = :$key";
        }

        $set = implode(", ", $set);
        return $db->query("UPDATE scooters SET $set WHERE id = :id",  $attrs);
    }
}