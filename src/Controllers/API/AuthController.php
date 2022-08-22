<?php

namespace App\Controllers\API;

use App\Controllers\BaseController;
use App\Models\Response;
use App\Models\Users;

class AuthController extends BaseController 
{
    public function login() 
    {
        $response = [ "status" => false, "message" => "", "data" => []]; 
        if( !isset($_REQUEST["email"]) || empty($_REQUEST["email"]) || !isset($_REQUEST["password"]) || empty($_REQUEST["password"])) {
            $response["message"] = "Aucun/certains identifiants n'ont pas été transmits.";
            return Response::json(200, [], $response);
        }

        $userInfo = [ "email" => $_REQUEST["email"], "password" => $_REQUEST["password"]];
        $responseLogin = Users::login($userInfo);
        if(!$responseLogin["status"]) {
            $response["message"] = "Identifiants invalides";
            $response["data"] = $responseLogin;
            return Response::json(200, [], $response);
        }

        $user = Users::getByMail($userInfo["email"]);
        $token = Users::newToken($user["id"]);

        $response["status"] = true;
        $response["message"] = "Identifiants valides";
        $response["data"]["user"] = $user;
        $response["data"]["token"] = $token;

        return Response::json(200, [], $response);
    }

    public function isConnectedByToken()
    {
        $response = [ "status" => false, "message" => "", "data" => []]; 
        if( !isset($_REQUEST["token"]) || empty($_REQUEST["token"])) {
            $response["message"] = "Aucun token n'a été transmit.";
            return Response::json(401, [], $response);
        }

        $user = Users::getOneByToken($_REQUEST["token"]);

        if($user == null) {
            $response["message"] = "Utilisateur non identifié";
            return Response::json(403, [], $response);
        }

        $response["status"] = true;
        $response["message"] = "L'utilisateur possède le bon token";
        return Response::json(200, [], $response);
    }
}