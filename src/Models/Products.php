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

    public static function add($productInfo)
    {     
        $db = new Database();

        try {
            $q = "INSERT INTO products(name, description, price, isPromotion, promotion, quantity) VALUES(:name, :description, :price, :isPromotion, :promotion, :quantity)";
            $res = $db->query($q, $productInfo);
            $data["boxMsgs"] = [["status" => "Succès", "class" => "success", "description" => "Le produit a bien été ajouté."]];
            $data["status"] = true;
        } catch (\Exception $e) {
            $data["boxMsgs"] = [["status" => "Erreur", "class" => "error", "description" => $e->getMessage()]];
            $data["status"] = false;
        }

        return $data;  
    }

    public static function updateOneById(array $infos)
    {
        $db = new Database();
        $q = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = N'products' AND TABLE_SCHEMA = ?";
        $acceptedFields = array_column($db->queryAll($q, [$_ENV["DB_NAME"]]), "COLUMN_NAME");

        $product = isset($infos["id"]) ? Products::get($infos["id"]) : null;
        if($product == null) return false;

        $set = [];
        $attrs["id"] = $infos["id"];
        foreach($infos as $key => $value) {
            if(!in_array($key, $acceptedFields) || $value == $product[$key]) {
                continue;
            }

            $attrs[$key] = $value;
            $set[] = "$key = :$key";
        }

        $set = implode(", ", $set);
        return $db->query("UPDATE products SET $set WHERE id = :id",  $attrs);
    }
}