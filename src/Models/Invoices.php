<?php

namespace App\Models;

use App\Database\Database;


class Invoices
{
    public static function get(int $id)
    {
        if($id == null || $id <= 0) return null;

        $db = new Database();
        $q = "SELECT * FROM invoices WHERE id = ?";

        $res = $db->queryOne($q, [$id]) ?: null;
        return $res;
    }

    public static function getAll(int $start = null, int $total = null)
    {
        $db = new Database();
        $q = "SELECT * FROM invoices";

        $res = null;
        if(!($start == null || $start < 0 || $total == null || $total < 0 )) {
            $q = $q . " LIMIT " . $start . ", " . $total; 
            $res = $db->queryAll($q);
        }

        $res = $db->queryAll($q) ?: null;

        return $res;
    }

    public static function add($invoice){
        $db = new Database();

        try {
            $q = "INSERT INTO invoices(invoiceN, invoiceDate, invoiceLink, idUser) VALUES(:invoiceN, :invoiceDate, :invoiceLink, :idUser)";
            $db->query($q, $invoice);
            $data["boxMsgs"] = [["status" => "Succès", "class" => "success", "description" => "Facture bien ajouté."]];
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
        $q = "DELETE FROM invoices WHERE id = ?";

        $res = $db->query($q, [$id]);

        return $res;
    }

    public static function updateOneById(array $infos)
    {
        $db = new Database();
        $q = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = N'invoices' AND TABLE_SCHEMA = ?";
        $acceptedFields = array_column($db->queryAll($q, [$_ENV["DB_NAME"]]), "COLUMN_NAME");

        $invoice = isset($infos["id"]) ? Invoices::get($infos["id"]) : null;
        if($invoice == null) return false;

        $set = [];
        $attrs["id"] = $infos["id"];

        foreach($infos as $key => $value) {
            if(!in_array($key, $acceptedFields) || $value == $invoice[$key]) {
                continue;
            }

            $attrs[$key] = $value;
            $set[] = "$key = :$key";
        }
        
        
        $set = implode(", ", $set);
        return $db->query("UPDATE invoices SET $set WHERE id = :id",  $attrs);
    }

    public static function getNewInvoiceNumber() 
    {
        $db = new Database();
        $q = "SELECT * FROM invoices WHERE invoiceN = ?";

        do {
            $invoiceN = strtoupper(bin2hex(random_bytes(4)));
        } while ($db->queryOne($q, [$invoiceN]));

        return $invoiceN;
    }
    
}