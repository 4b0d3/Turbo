<?php 

namespace App\Models;

use App\Database\Database;

class Rides {
    public static function get(int $id)
    {
        $db = new Database();
        $q = "SELECT * FROM rides WHERE id = ?";

        return $db->queryOne($q, [$id]) ?: null;;
    }

    public static function getAllByUserId(int $id)
    {
        $db = new Database();
        $q = "SELECT * FROM rides WHERE idUser = ?";

        return $db->queryAll($q, [$id]) ?: null;;
    }

    public static function getFull(int $id)
    {
        $db = new Database();
        $q = "SELECT *, r.id as id FROM rides AS r INNER JOIN users AS u ON r.idUser = u.id INNER JOIN scooters AS s ON r.idScooter = s.id WHERE r.id = ?";

        return $db->queryOne($q, [$id]) ?: null;
    }

    public static function getLastIdRideByScooterId(int $id)
    {
        $db = new Database();
        $q = "SELECT MAX(id) as id FROM rides WHERE idScooter = ?";

        $res = $db->queryOne($q, [$id]);
        return $res ? $res["id"]: null;
    }

    public static function start(array $infos) 
    {
        $db = new Database();
        $q = "INSERT INTO rides(idUser, idScooter, startLat, startLong) VALUES(:idUser, :idScooter, :startLat, :startLong)";

        return $db->query($q, $infos);
    }

    public static function end(array $infos) 
    {
        $db = new Database();
        $q = "UPDATE rides SET endLat = :endLat, endLong = :endLong, price = :price, isPayed = :isPayed, endTime = :endTime WHERE id = :id";

        return $db->query($q, $infos);
    }

}