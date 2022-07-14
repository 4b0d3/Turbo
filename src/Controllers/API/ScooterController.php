<?php

namespace App\Controllers\API;

use App\Controllers\BaseController;
use App\Models\Response;
use App\Models\Rides;
use App\Models\Scooters;
use App\Models\Users;

class ScooterController extends BaseController 
{

    public static function getAllDisponibles() 
    {
        $scooters = Scooters::getAllDisponibles();

        $data = [
            "status" => true,
            "data" => [
                "scooters" => $scooters
            ]
            ];

        return Response::json(200, [], $data);
    }

    // IMPLEM : Gérer le token d'authentification pour l'API
    /**
     * @path /api/ride/start/
     */
    public function startRide()
    {
        if(!isset($_REQUEST["idScooter"]) || !isset($_REQUEST["idUser"])) return Response::json(400, [], ["status" => "NO", "message" => "Informations are missing."]);

        $scooter = Scooters::get($_REQUEST["idScooter"]);
        $user = Users::get($_REQUEST["idUser"]);
        if($scooter == null || $user == null) return Response::json(404, [], ["status" => "NO", "message" => "Wrong informations."]);

        if($scooter["inUse"] != 0 || $scooter["status"] != "activé") return Response::json(404, [], ["status" => "NO", "message" => "Scooter not available."]);
        if($user["role"] == "banned" || $user["confirmed"] != "1") return Response::json(404, [], ["status" => "NO", "message" => "User not allowed."]);

        Rides::start(["idUser" => $user["id"], "idScooter" => $scooter["id"], "startLat" => $scooter["latitude"], "startLong" => $scooter["longitude"]]);
        Scooters::putInUse($scooter["id"]);
        // Mettre en place le compteur de 30 minutes
        return Response::json(200, [], ["status:" => "OK", "message" => "Scooter is enabled."]);
    }

    /**
     * @path /api/ride/stop/
     */
    public function stopRide()
    {
        if(!isset($_REQUEST["idScooter"]) || !isset($_REQUEST["idUser"])) return Response::json(400, [], ["status" => "NO", "message" => "Informations are missing."]);

        $scooter = Scooters::get($_REQUEST["idScooter"]);
        $user = Users::get($_REQUEST["idUser"]);
        $ride = Rides::get(Rides::getLastIdRideByScooterId($scooter["id"]));
        if($scooter == null || $user == null || $ride == null || $ride["idUser"] != $user["id"]) return Response::json(404, [], ["status" => "NO", "message" => "Wrong informations."]);
        Scooters::putInUse($scooter["id"], 0); // passer inUse à 0

        $startTime = new \DateTime($ride["startTime"]);
        $now = new \DateTime("now");
        // $now->add(new \DateInterval("PT500M"));
        isset($_REQUEST["time"]) ? $now->add(new \DateInterval("PT" . $_REQUEST["time"] . "M")) : "";
        $rideTime = $startTime->diff($now);
        $rideTime = ($rideTime->days * 24 * 60) + ($rideTime->h * 60) + ($rideTime->i);

        $price = 0;
        $userUpdate["id"] = $user["id"];
        if($user["sub"] == 1)
        {
            if($rideTime > $user["timeRemaining"]) {
                $price += ($rideTime-$user["timeRemaining"])*0.23;
            }
            $userUpdate["timeRemaining"] = 0;
        } elseif (in_array($user["sub"], [2,3,4])) {
            if($rideTime > $user["timeRemaining"]) {
                $price += ($rideTime-$user["timeRemaining"])*0.23;
                $userUpdate["timeRemaining"] = 0;
            } else {
                $rideTime = $rideTime + (30-$rideTime%30);
                $userUpdate["timeRemaining"] = $user["timeRemaining"] - $rideTime;
            }
        }

        // Arrêter la fonction update()
        $price = number_format((float)round($price, 2), 2, '.', '') ;
        $scooterUpdate = ["id" => $ride["id"], "endLat" => $scooter["latitude"], "endLong" => $scooter["longitude"], "price" => $price, "endTime" => $now->format("Y-m-d H:i:s")];
        Users::updateOneById($userUpdate);
        if($price > 0) {
            $scooterUpdate["isPayed"] = 0;
            Rides::end($scooterUpdate);
            return Response::json(200, [], ["status:" => "PAY", "price" => $price]);
        } else {
            $scooterUpdate["isPayed"] = 1;
            Rides::end($scooterUpdate);
            return Response::json(200, [], ["status:" => "OK", "message" => "Trajet bien terminé."]);
        }
    }

    public function update()
    {

        /*
        Requête pour update : 
        - La position
        - Le niveau de batterie
        - Trottinette utilisée ou non
        - Temps restant < 30 min (donc ne pas utiliser de compte à rebours mais plutôt comparer un minuteur afin que ce
        dernier soit inférieur à 30 min. Si supérieur, appeler stopRide)
        */

    }
}