<?php 

namespace App\Models;

use App\Database\Database;
use App\Models\Roles;
use App\Entity\FormChecker;
use App\Entity\User;

class Users {
    public static function get(int $id)
    {
        if($id == null || $id <= 0) return null;

        $db = new Database();
        $q = "SELECT users.*, roles.name as role FROM users LEFT JOIN roles ON users.role = roles.id WHERE users.id = ?";

        return $res = $db->queryOne($q, [$id]) ?: null;
    }

    public static function getByMail(string $email)
    {
        $db = new Database();
        $q = "SELECT users.*, roles.name as role FROM users LEFT JOIN roles ON users.role = roles.id WHERE users.email = ?";

        $res = $db->queryOne($q, [$email]) ?: null;
        return $res;
    }

    public static function getAll(int $start = null, int $total = null)
    {
        $db = new Database();
        $q = "SELECT users.*, roles.name as role FROM users LEFT JOIN roles ON users.role = roles.id";

        $res = null;
        if(!($start == null || $start < 0 || $total == null || $total < 0 )) {
            $q = $q . " LIMIT " . $start . ", " . $total; 
            $res = $db->queryAll($q);
        }

        $res = $db->queryAll($q) ?: null;

        return $res;
    }

    public static function add(array $user) :array
    {
        $data = [];
        $fields = [
            [ "type" => "email", "name" => "email" ],
            [ "type" => "email", "name" => "confirmEmail" ],
            [ "type" => "name", "name" => "name" ],
            [ "type" => "firstName", "name" => "firstName" ],
            [ "type" => "password", "name" => "password" ],
            [ "type" => "password", "name" => "confirmPassword" ],
            [ "type" => "role", "name" => "role" ],
        ];

        $data = (new FormChecker)->check($fields, $user);

        if(!$data["status"]) {
            return $data;
        }

        $user = $data["form"]["checkedFields"];
        $alreadyExists = Users::getByMail($user["email"]);

        if($alreadyExists != null) {
            $data["status"] = false;
            $data["boxMsgs"] = [["status" => "Erreur", "class" => "error", "description" => "L'utilisateur n'a pas été créé : l'adresse mail est déjà utilisée."]];
            return $data;
        }   

        $db = new Database();
        $q = "INSERT INTO users(email, password, name, firstName, role) VALUES(:email, :password, :name, :firstName, :role)";

        $user["password"] = password_hash($user["password"], PASSWORD_DEFAULT);

        $res = $db->query($q, $user);

        if(!$res) {
            $data["status"] = false;
            $data["boxMsgs"] = [["status" => "Erreur", "class" => "error", "description" => "L'utilisateur n'a pas été créé : problème lors de la requête d'ajout de l'utilisateur dans la base de données."]];
            return $data;
        }

        $data["boxMsgs"] = [["status" => "Succès", "class" => "success", "description" => "L'utilisateur a bien été créé."]];
        return $data;
    }

    public static function updateOneById(array $infos)
    {
        $db = new Database();
        $q = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = N'users' AND TABLE_SCHEMA = ?";
        $acceptedFields = array_column($db->queryAll($q, [$_ENV["DB_NAME"]]), "COLUMN_NAME");

        $user = isset($infos["id"]) ? Users::get($infos["id"]) : null;
        if($user == null) return false;

        $set = [];
        $attrs["id"] = $infos["id"];
        foreach($infos as $key => $value) {
            if(!in_array($key, $acceptedFields) || $value == $user[$key]) {
                continue;
            }

            if($key == "password") {
                if(!empty($value)) {
                    $value = password_hash($value, PASSWORD_DEFAULT);
                } else {
                    continue;
                }
            }

            $attrs[$key] = $value;
            $set[] = "$key = :$key";
        }

        $set = implode(", ", $set);
        return $db->query("UPDATE users SET $set WHERE id = :id",  $attrs);
    }

    public static function delete(int $id) :bool
    {
        if($id == null && $id <= 0) return false;

        $db = new Database();
        $q = "DELETE FROM users WHERE id = ?";

        $res = $db->query($q, [$id]);

        return $res;
    }

    public static function login(array $user)
    {
        $data = [];
        $fields = [
            [ "type" => "email", "name" => "email" ],
            [ "type" => "password", "name" => "password" ],
        ];

        $data = (new FormChecker)->check($fields, $user, "Impossible de se connecter");

        if(!$data["status"]) {
            return $data;
        }

        $user = $data["form"]["checkedFields"];
        $getUser = Users::getByMail($user["email"]);
        
        if($getUser != null && $getUser["role"] == "banned") {
            $data["status"] = false;
            $data["boxMsgs"] = [["status" => "Erreur", "class" => "error", "description" => "Voter compte a été banni."]];
            return $data;
        }

        if($getUser != null && !$getUser["confirmed"]) {
            $data["status"] = false;
            $data["boxMsgs"] = [["status" => "Erreur", "class" => "error", "description" => "Votre compte n'a pas été confirmé."]];
            return $data;
        }
        
        if($getUser != null && password_verify($user["password"], $getUser["password"])) {
            $_SESSION["id"] = $getUser["id"];
            Cart::mergeCartCookies();
            return $data;
        }

        $data["status"] = false;
        $data["boxMsgs"] = [["status" => "Erreur", "class" => "error", "description" => "Idientifiants invalides"]];
        return $data;
    }

    public static function changeSub(int $id, int $userId = null)
    {
        $db = new Database();
        $q = "UPDATE users SET sub = ?, timeRemaining = ?, subExpire = ? WHERE id = ?";

        $userId = $userId ?: (new User())->get("id");

        $sub = Subscriptions::get($id);
        $time = 30 * $sub["openings"];
        
        // $sub = Subscriptions::get($id);
        // $time = 30 * $sub["openings"];
        $nextMonth = time() + (30 * 24 * 60 * 60);
        $dateExp = date('Y-m-d', $nextMonth);

        return $db->query($q, [$id, $time, $dateExp, $userId]);
    }

    public static function changeTurboz(int $sum, int $userId = null){
        $db = new Database();
        $q = "UPDATE users SET turboz = turboz + ? WHERE id = ?";

        $turboz = $sum / 10;
        $turboz = floor($turboz);
        $turboz= $turboz * 5;

        return $db->query($q, [$turboz, $userId]);

    }

    public static function getSub(int $id, int $userId = null) 
    {
        $db = new Database();
        $q = "SELECT * FROM users WHERE id = ?";

        $userId = $userId ?: (new User())->get("id");

        $res = $db->query($q, [$id, $userId]);
        if($res == false) return null;

        return $res["sub"];
    }

    public static function isSub(int $id, int $userId = null)
    {
        $db = new Database();
        $q = "SELECT * FROM users WHERE id = ?";

        $userId = $userId ?: (new User())->get("id");

        $res = $db->query($q, [$id, $userId]);
        if($res == false) return;

        return !intval($res["sub"]);
    }

    public static function deleteSub($idUser) 
    {
        $db = new Database();
        $q = "UPDATE users SET sub = ?, subExpire = ?, timeRemaining = ? WHERE id = ?";

        return $db->query($q, [0, NULL, 0, $idUser]);
    }

    public static function getAllAddresses(int $idUser, int $start = null, int $total = null)
    {
        $db = new Database();
        $q = "SELECT * FROM addresses WHERE idUser = ?";

        $res = null;
        if(!($start == null || $start < 0 || $total == null || $total < 0 )) {
            $q = $q . " LIMIT " . $start . ", " . $total; 
            $res = $db->queryAll($q, [$idUser]);
        }

        $res = $db->queryAll($q, [$idUser]) ?: null;

        if($res != null) {
            foreach($res as $key => $address) {
                if($address["isMain"] == "1") {
                    $out = array_splice($res, $key, 1);
                    array_splice($res, 0, 0, $out);
                }
            }
        }

        return $res;
    }

    public static function getOneAddress(int $idAddress)
    {
        $db = new Database();
        $q = "SELECT * FROM addresses WHERE id = ? ";

        return $db->queryOne($q, [$idAddress]) ?: null;
    }

    public static function getMainUserAddress(int $idUser)
    {
        $db = new Database();
        $q = "SELECT * FROM addresses WHERE idUser = ? AND isMain = 1";

        return $db->queryOne($q, [$idUser]) ?: null;
    }

    public static function addAddress($idUser, $address) 
    {
        $db = new Database();
        $q = "INSERT INTO addresses(idUser, country, city, address, zipcode, additional, isMain) VALUES(:idUser, :country, :city, :address, :zipcode, :additional, :isMain) ";

        $params = [
            "country" => $address["country"],
            "city" => $address["city"],
            "address" => $address["address"],
            "zipcode" => $address["zipcode"],
            "additional" => $address["additional"],
            "isMain" => $address["isMain"]
        ];
        $params["idUser"] = $idUser;
        $res = $db->query($q, $params);

        if($res && isset($address["isMain"]) && $address["isMain"] == "1") {
            $lastId = $db->getPDO()->lastInsertId();
            $q = "UPDATE addresses SET isMain = 0 WHERE idUser = ? AND id != ?";
            $res = $db->query($q, [$idUser, $lastId]);
        }

        return $res; 
    }

    public static function setFavAddresss($idUser, $idAddress) 
    {
        $db = new Database();
        $q = "UPDATE addresses SET isMain = 0 WHERE idUser = ?";
        $res = $db->query($q, [$idUser]);

        if($res != null) {
            $q = "UPDATE addresses SET isMain = 1 WHERE idUser = ? AND id = ?";
            $res = $db->query($q, [$idUser, $idAddress]);
        }

        return $res;
    }

    public static function deleteAddress($idAddress) 
    {
        $db = new Database();
        $q = "DELETE FROM addresses WHERE id = ?";
        return $db->query($q, [$idAddress]);
    }

    public static function newToken($idUser) 
    {
        $user = Users::get($idUser);

        if($user == null) return null;

        $token = bin2hex(random_bytes(16));

        return Users::updateOneById(["id" => $idUser, "token" => $token]) ? $token : null;
    }

    public static function getOneByToken($token) 
    {
        if(empty($token)) return null;

        $db = new Database();
        $user = $db->queryOne("SELECT * FROM users WHERE token = :token", ["token" => $token]);

        return $user ? $user : null;
    }

    public static function getAllInvoices(int $idUser, int $start = null, int $total = null)
    {
        $db = new Database();
        $q = "SELECT * FROM invoices WHERE idUser = ? ORDER BY invoiceDate DESC";

        $res = null;
        if(!($start == null || $start < 0 || $total == null || $total < 0 )) {
            $q = $q . " LIMIT " . $start . ", " . $total; 
            $res = $db->queryAll($q, [$idUser]);
        }

        $res = $db->queryAll($q, [$idUser]) ?: null;

        return $res;
    }
    
    public static function addPartner($PartnerInfos)
    {
        $db = new Database();
        $q = "INSERT INTO partners(name, description, price, promoCode ) VALUES(:name, :description, :price, :promoCode)";
        return $db->query($q, $PartnerInfos);
    }
    
    public static function addVerifToken($token, $email)
    {
        $db = new Database();
        $q = "UPDATE users SET token= ? WHERE email= ?";
        return $db->query($q, [$token, $email]);
    }

    public static function checkAccount($email)
    {
        $db = new Database();
        $q = "SELECT token,confirmed FROM users WHERE email= ?";
        return $db->queryOne($q, [$email]);
    }

    public static function verifAccount($email)
    {
        $db = new Database();
        $q = "UPDATE users SET confirmed = 1 WHERE email= ?";
        return $db->query($q, [$email]);
    }

    public static function getAllRides(int $idUser, int $start = null, int $total = null)
    {
        $db = new Database();
        $q = "SELECT * FROM rides WHERE idUser = ?";

        $res = null;
        if(!($start == null || $start < 0 || $total == null || $total < 0 )) {
            $q = $q . " LIMIT " . $start . ", " . $total; 
            $res = $db->queryAll($q, [$idUser]);
        }

        $res = $db->queryAll($q, [$idUser]) ?: null;

        return $res;
    }

    public static function getTurboz(int $idUser)
    {
        $db = new Database();
        $q = "SELECT turboz FROM users WHERE id = ?";

        $res = $db->queryOne($q, [$idUser]) ?: null;

        return $res;
    }

}