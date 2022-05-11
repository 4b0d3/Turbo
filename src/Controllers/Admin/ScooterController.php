<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\Scooters;
use App\Database\Database;

class ScooterController extends BaseController 
{

    public function get() {
        $data = ["edit" => "Edit | Turbo"];
        $data["header"]["admin"] = 1;
        $this->display("Admin/updateScooter.html.twig", $data); 
    }

    public function post()
    {
        $data = [];

        if(!isset($_POST["geo"]) || empty($_POST["geo"])) {
            $data["error"]["message"] = "Veuillez renseignez la geo";
        }
         if(!isset($_POST["battery"]) || empty($_POST["battery"])) {
            $data["error"]["message"] = "Veuillez renseignez le pourcentage de la battery";
        }

        if (!array_key_exists("error", $data)) {
            $db = new Database();
            $newGeo = $_POST["geo"];
            $newBattery = $_POST["battery"];
            $id = $this->match["params"]["id"];
            $attrs =[
                "newgeolocalisation" => $newGeo,
                "newbattery" => $newBattery,
                "id" => $id
            ];
            $req = $db->query("UPDATE scooters SET geolocalisation = :newgeolocalisation, battery = :newbattery where id = :id", $attrs);
            if ($req !=false) {
                $data["success"]["message"] = "Tout est OK";
            }else{
                $data["error"]["message"] = "error 3";
            }
                
        }else{
            $data["error"]["message"] = "error 1";
        }
        

        $this->display("admin/updateScooter.html.twig", $data);
    }
        

    

    public function delete()
    {
    
            $data["scooters"] = Scooters::delete($this->match["params"]["id"]);
            $data["header"]["admin"] = 1;   
            $this->display("Admin/scooters.html.twig", $data);
            
    }
}