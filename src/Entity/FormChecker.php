<?php 

namespace App\Entity;

use App\Models\Roles;

class FormChecker {


    public function __construct()
    {

    }

    public function check(array $fields, array $posts, string $entName = "L'utilisateur")  
    {
        $data = [];
        $allowedTypes = ["email", "password", "name", "firstName", "role", "rolename", "checkbox", "int"];

        foreach ($fields as $field) {
            $type = $field["type"];
            $name = $field["name"];

            if (!in_array($type, $allowedTypes)) {
                if(!isset($data["boxMsgs"])) { $data["boxMsgs"] = []; }
                array_push($data["boxMsgs"], ["status" => "Erreur", "class" => "error", "description" => "$entName n'a pas été créé : Le type de champs $type n'est pas autorisé."]);
                continue;
            }

            $action = "check" . ucfirst($type);
            if(!method_exists($this, $action)) {
                if(!isset($data["boxMsgs"])) { $data["boxMsgs"] = []; }
                array_push($data["boxMsgs"], ["status" => "Erreur", "class" => "error", "description" => "$entName n'a pas été créé : La méthode permettant de vérifier le champ $type n'existe pas"]);
                continue;
            }

            $res = $this->$action($name, $posts);
            if((!isset($data["boxMsgs"]) || empty($data["boxMsgs"])) && isset($res["error"][$name])) {
                if(!isset($data["boxMsgs"])) { $data["boxMsgs"] = []; }
                $data["boxMsgs"][] = ["status" => "Erreur", "class" => "error", "description" => "$entName n'a pas été créé : " . $res["error"][$name]];
            }
            $data = array_merge_recursive($data, $res); 
        }
        
        if(isset($data["error"]) || isset($data["boxMsgs"])) {
            $data["status"] = false;
        } else {
            $data["status"] = true;
        }

        return $data;
    }

    public function checkEmail($name, $posts)
    {
        $cm = $name == "confirmEmail" ? true : false;

        $data = [];
        if(!isset($posts[$name]) || empty($posts[$name])) {
            $data["error"][$name] = !$cm ? "Veuillez renseigner une adresse mail." : "Veuillez confirmer votre adresse mail.";
        } else if(!filter_var($posts[$name], FILTER_VALIDATE_EMAIL) && !$cm) {
            $data["error"][$name] = "Le format du mail ne respecte pas les standards.";
        } else if(strlen($posts[$name]) > 255 && !$cm) {
            $data["error"][$name] = "La longueur maximum du mail doit être de 255 caractères.";
        } else if($cm && ($posts[$name] != $posts["email"])) {
            $data["error"][$name] = "Le mail de confirmation est différent du mail principal.";
        } else {
            !$cm ? $data["checkedFields"][$name] = $posts[$name] : "";
        }

        return $data;
    }

    public function checkPassword($name, $posts) 
    {
        $cp = $name == "confirmPassword" ? true : false;

        $data = [];
        if(!isset($posts[$name]) || empty($posts[$name])) {
            $data["error"][$name] = !$cp ? "Veuillez renseigner un mot de passe." : "Veuillez confirmer votre mot de passe.";
        } else if(preg_match("/^(?=.*\d)(?=.*[A-Za-z])[0-9A-Za-z!@#$%]{4,12}$/", $posts[$name]) && !$cp ) {
            $data["error"][$name] = "Le mot de passe n'a pas le bon format.";
        } else if(strlen($posts[$name]) > 255 && !$cp) { // Shorter than 255
            $data["error"][$name] = "Le mot de passse est trop long.";
        } else if($cp && ($posts[$name] != $posts["password"])) {
            $data["error"][$name] = "Les mots de passe sont différents.";
        } else {
            !$cp ? $data["checkedFields"][$name] = $posts[$name] : "";
        }

        return $data;
    }

    public function checkFirstName($name, $posts)
    {
        if(!isset($posts[$name]) || empty($posts[$name])) {
            $data["error"][$name] = "Veuillez renseigner un prénom.";
        } else {
            $data["checkedFields"][$name] = $posts[$name];
        }

        return $data;
    }

    public function checkName($name, $posts)
    {
        if(!isset($posts[$name]) || empty($posts[$name])) {
            $data["error"][$name] = "Veuillez renseigner un nom.";
        } else {
            $data["checkedFields"][$name] = $posts[$name];
        }

        return $data;
    }

    public function checkRole($name, $posts) {
        $roles = Roles::getAll();
        
        if(!isset($posts[$name]) || empty($posts[$name])) {
            $data["error"][$name] = "Veuillez renseigner un role.";
        } else if (!in_array($posts[$name], array_column($roles, "id"))) {
            $data["error"][$name] = "Le role spécifié n'existe pas.";
        } else {
            $data["checkedFields"][$name] = $posts[$name];
        }

        return $data;
    }

    public function checkCheckbox($name, $posts) 
    {
        $cm = $name == "confirmEmail" ? true : false;

        $data = [];
        if(!isset($posts[$name]) || empty($posts[$name])) {
            $data["error"][$name] = !$cm ? "Veuillez renseigner une adresse mail." : "Veuillez confirmer votre adresse mail.";
        } else if(!filter_var($posts[$name], FILTER_VALIDATE_EMAIL) && !$cm) {
            $data["error"][$name] = "Le format du mail ne respecte pas les standards.";
        } else if(strlen($posts[$name]) > 255 && !$cm) {
            $data["error"][$name] = "La longueur maximum du mail doit être de 255 caractères.";
        } else if($cm && ($posts[$name] != $posts["email"])) {
            $data["error"][$name] = "Le mail de confirmation est différent du mail principal.";
        } else {
            !$cm ? $data["checkedFields"][$name] = $posts[$name] : "";
        }

        return $data;
    }

    public function checkRolename($name, $posts)
    {

        $data = [];
        if(!isset($posts[$name]) || empty($posts[$name])) {
            $data["error"][$name] = "Veuillez renseigner le nom du role" ;
        } else if(strlen($posts[$name]) > 255) {
            $data["error"][$name] = "La longueur maximum du rôle doit être de 255 caractères.";
        } else if (Roles::getId($posts["name"])) {
            $data["error"][$name] = "Le nom du rôle est déjà utilisé, il doit être unique.";
        } else {
            $data["checkedFields"][$name] = $posts[$name];
        }
        return $data; 
    }

}