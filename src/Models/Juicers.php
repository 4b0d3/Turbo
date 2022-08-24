<?php

namespace App\Models;

use App\Database\Database;

class Juicers {
    public static function get(int $id)
    {
        if($id == null || $id <= 0) return null;

        $db = new Database();
        $q = "SELECT * FROM scooters WHERE id = ?";

        $res = $db->queryOne($q, [$id]) ?: null;
        return $res;
    }

    public static function getAll(int $battery = null)
    {
        $db = new Database();
        
        $q = "SELECT * FROM scooters where battery <= $battery";
        
        $res = null;
        
        $res = $db->queryAll($q);


        $res = $db->queryAll($q) ?: null;

        return $res;
    }

    public static function updateOneById(array $infos)
    {
        $db = new Database();
        $q = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = N'scooters' AND TABLE_SCHEMA = ?";
        $acceptedFields = array_column($db->queryAll($q, [$_ENV["DB_NAME"]]), "COLUMN_NAME");

        $scooter = isset($infos["id"]) ? Juicers::get($infos["id"]) : null;
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

    public static function chargeScooter($idScooter) {
        $db = new Database();
        $q = "UPDATE scooters SET battery = ? WHERE id = ?";

        return $db->query($q, [100, $idScooter]);
    }
}