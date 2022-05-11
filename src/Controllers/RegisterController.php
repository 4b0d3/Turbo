<?php

namespace App\Controllers;
use \App\Models\Users;

class RegisterController extends BaseController 
{

    public function get($data = []) {
        $this->display("register.html.twig", $data);
    }

    public function post($data = []) {

        $checkFields = $this->verifRegisterFields();
        foreach ($checkFields as $key => $value) { $data[$key] = $value; }

        if(!array_key_exists("error", $data)) {
            unset($data["form"]); // No need to recomplete form again so it delete info
            Users::add($data["user"]);
            /**
             * TOMAKE
             * Rediriger vers envoie de mail
             */
            return;
        }

        $this->display("register.html.twig", $data);
        return;
    }

    private function verifRegisterFields() :array
    {
        /**
         * TOMAKE
         * Vérifier les champs
         */
        $data = [];

        if(!isset($_POST["email"]) || empty($_POST["email"])) {
            $data["error"]["email"] = "Veuillez renseignez une addresse mail";
        } else if(!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
            $data["error"]["email"] = "Format du mail incorrect";
        } else if(strlen($_POST["email"]) > 255) {
            $data["error"]["email"] = "Mail trop long";
        } else {
            $data["form"]["email"] = $_POST["email"];
            $data["user"]["email"] = $_POST["email"];
        }
        
        if(!isset($_POST["confirmEmail"]) || empty($_POST["confirmEmail"])) { // Exists and is not empty
            $data["error"]["confirmEmail"] = "Veuillez confirmer votre addresse mail";
        } else if($_POST["email"] != $_POST["confirmEmail"]) { // Same as the mail
            $data["error"]["confirmEmail"] = "Le mail de confirmation est différent";
        } else if(strlen($_POST["confirmEmail"]) > 255) { // Shorter than 255
            $data["error"]["confirmEmail"] = "Mail de confirmation trop long";
        }

        if(!isset($_POST["password"]) || empty($_POST["password"])) { // Exists and is not empty
            $data["error"]["password"] = "Veuillez confirmer votre mot de passe";
        } else if(preg_match("/^(?=.*\d)(?=.*[A-Za-z])[0-9A-Za-z!@#$%]{4,12}$/", $_POST["password"])) {
            $data["error"]["password"] = "Le mot de passe n'a pas le bon format";
        } else if(strlen($_POST["password"]) > 255) { // Shorter than 255
            $data["error"]["password"] = "Le mot de passse est trop long";
        } else {
            $data["user"]["password"] = $_POST["password"];
        }

        if(!isset($_POST["confirmPassword"]) || empty($_POST["confirmPassword"])) { // Exists and is not empty
            $data["error"]["confirmPassword"] = "Veuillez confirmer votre addresse mail";
        } else if($_POST["confirmPassword"] != $_POST["confirmPassword"]) {
            $data["error"]["confirmPassword"] = "Le mot de passe de confirmation est différent";
        } else if(strlen($_POST["confirmPassword"]) > 255) { // Shorter than 255
            $data["error"]["confirmPassword"] = "Le mot de passe de confirmation est trop long";
        }

        if(!isset($_POST["firstName"]) || empty($_POST["firstName"])) {
            $data["error"]["firstName"] = "Veuillez renseignez une addresse un prénom";
        } else {
            $data["form"]["firstName"] = $_POST["firstName"];
            $data["user"]["firstName"] = $_POST["firstName"];
        }

        if(!isset($_POST["name"]) || empty($_POST["name"])) {
            $data["error"]["name"] = "Veuillez renseignez une addresse un nom";
        } else {
            $data["form"]["name"] = $_POST["name"];
            $data["user"]["name"] = $_POST["name"];
        }

        return $data;
    }
}