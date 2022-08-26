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

    public static function getAll(int $confirmed = null)
    {
        $db = new Database();
        if($confirmed == 1){
        $q = "SELECT * FROM partners where confirmed = $confirmed";
        }else $q = "SELECT * FROM partners";
        $res = null;
        
        $res = $db->queryAll($q);


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

    public static function getPrice(int $id){
        if($id == null || $id <= 0) return false;

        $db = new Database();
        $q = "SELECT price FROM partners WHERE id = ?";

        $res = $db->queryOne($q, [$id]);

        return $res;
    }

    public static function getTurboz(int $id){
        if($id == null || $id <= 0) return false;

        $db = new Database();
        $q = "SELECT turboz FROM users WHERE id = ?";

        $res = $db->queryOne($q, [$id]);

        return $res;
    }

    public static function buy(int $userID, int $result){

        $db = new Database();
        $q = "UPDATE users SET turboz= ? WHERE id = ?";

        $res = $db->query($q, [$result, $userID]);

        return $res;  
    }

    public static function getCode(int $id){

        $db = new Database();
        $q = "SELECT promoCode FROM partners WHERE id = ?";

        $res = $db->queryOne($q, [$id]);

        return $res;  
    }
}