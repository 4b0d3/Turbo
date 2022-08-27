<?php

namespace App\Models;

use App\Database\Database;
use Stripe\Subscription;

class Subscriptions
{
    public static function get(int $id)
    {
        if($id == null || $id <= 0) return null;

        $db = new Database();
        $q = "SELECT * FROM subscriptions WHERE id = ?";

        $res = $db->queryOne($q, [$id]) ?: null;
        return $res;
    }

    public static function getAll(int $start = null, int $total = null)
    {
        $db = new Database();
        $q = "SELECT * FROM subscriptions";

        $res = null;
        if(!($start == null || $start < 0 || $total == null || $total < 0 )) {
            $q = $q . " LIMIT " . $start . ", " . $total; 
            $res = $db->queryAll($q);
        }

        $res = $db->queryAll($q) ?: null;

        return $res;
    }

    public static function add($subscription){
        $db = new Database();

        try {
            $q = "INSERT INTO subscriptions(name, price, title, description) VALUES(:name, :price, :title, :description)";
            $db->query($q, $subscription);
            $data["boxMsgs"] = [["status" => "Succès", "class" => "success", "description" => "L'abonnement a bien été ajouté."]];
            $data["status"] = true;
        } catch (\Exception $e) {
            $data["boxMsgs"] = [["status" => "Erreur", "class" => "error", "description" => $e->getMessage()]];
            $data["status"] = false;
        }

        return $data;
    }

    public static function delete(int $id) :bool
    {
        if($id == null || $id <= 0) return false;

        $db = new Database();
        $q = "DELETE FROM subscriptions WHERE id = ?";

        $res = $db->query($q, [$id]);

        return $res;
    }

    public static function updateOneById(array $infos)
    {
        $db = new Database();
        $q = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = N'subscriptions' AND TABLE_SCHEMA = ?";
        $acceptedFields = array_column($db->queryAll($q, [$_ENV["DB_NAME"]]), "COLUMN_NAME");

        $subscription = isset($infos["id"]) ? Subscriptions::get($infos["id"]) : null;
        if($subscription == null) return false;

        $set = [];
        $attrs["id"] = $infos["id"];
        foreach($infos as $key => $value) {
            if(!in_array($key, $acceptedFields) || $value == $subscription[$key]) {
                continue;
            }

            $attrs[$key] = $value;
            $set[] = "$key = :$key";
        }

        $set = implode(", ", $set);
        return $db->query("UPDATE subscriptions SET $set WHERE id = :id",  $attrs);
    }
    
}