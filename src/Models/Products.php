<?php 

namespace App\Models;

use App\Database\Database;

class Products {
    public static function get(int $id)
    {
        if($id == null && $id <= 0) return null;

        $db = new Database();
        $q = "SELECT * FROM products WHERE id = ?";

        $res = $db->queryOne($q, [$id]) ?: null;
        if(!$res) return $res;

        $res["images"] = Products::getProductImages($id);


        return $res;
    }

    public static function getProductImages(int $id)
    {
        if($id == null && $id <= 0) return null;
        $db = new Database();
        $q = "SELECT * FROM product_medias AS pm INNER JOIN medias AS me ON pm.idMedia = me.id  WHERE pm.idProduct = ? ORDER BY pm.weight";
        $res = $db->queryAll($q, [$id]) ?: null;

        return $res;
    }

    public static function getAll(int $start = null, int $total = null)
    {
        $db = new Database();
        $q = "SELECT id FROM products";

        if($start != null && $start >= 0 && $total != null && $total >= 0) $q = $q . " LIMIT " . $start . ", " . $total; 

        $ids = $db->queryAll($q) ?: null;
        if(!$ids) return null;
        
        $res = [];
        foreach(array_column($ids, "id") as $id) {
            $product = Products::get($id);
            $product ? array_push($res, $product) : "";
        }

        return $res;
    }
}