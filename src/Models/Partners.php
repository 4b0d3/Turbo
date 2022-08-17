<?php

namespace App\Models;

use App\Database\Database;

class Partners {
    public static function get(int $id)
    {
        if($id == null || $id <= 0) return null;

        $db = new Database();
        $q = "SELECT * FROM partners WHERE id = ?";

        $res = $db->queryOne($q, [$id]) ?: null;
        return $res;
    }

    public static function getAll(int $start = null, int $total = null)
    {
        $db = new Database();
        $q = "SELECT * FROM partners";

        $res = null;
        if(!($start == null || $start < 0 || $total == null || $total < 0 )) {
            $q = $q . " LIMIT " . $start . ", " . $total; 
            $res = $db->queryAll($q);
        }

        $res = $db->queryAll($q) ?: null;

        return $res;
    }

    public static function updateOneById(array $infos)
    {
        $db = new Database();
        $q = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = N'partners' AND TABLE_SCHEMA = ?";
        $acceptedFields = array_column($db->queryAll($q, [$_ENV["DB_NAME"]]), "COLUMN_NAME");

        $partner = isset($infos["id"]) ? Partners::get($infos["id"]) : null;
        if($partner == null) return false;

        $set = [];
        $attrs["id"] = $infos["id"];
        foreach($infos as $key => $value) {
            if(!in_array($key, $acceptedFields) || $value == $partner[$key]) {
                continue;
            }

            $attrs[$key] = $value;
            $set[] = "$key = :$key";
        }

        $set = implode(", ", $set);
        return $db->query("UPDATE partners SET $set WHERE id = :id",  $attrs);
    }

    public static function delete(int $id) :bool
    {
        if($id == null || $id <= 0) return false;

        $db = new Database();
        $q = "DELETE FROM partners WHERE id = ?";

        $res = $db->query($q, [$id]);

        return $res;
    }
}