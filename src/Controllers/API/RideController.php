<?php

namespace App\Controllers\API;

use App\Controllers\BaseController;
use App\Models\Response;
use App\Models\Scooters;

class RideController extends BaseController 
{

    // IMPLEM : Gérer le token d'authentification pour l'API
    public function startRide()
    {

        // passer inUse à 1
        // Mettre en place le compteur de 30 minutes
        // Lier la trottinette à l'utilisateur en BDD
    }

    public function stopRide()
    {
        // passer inUse à 0
        // Décompter le temps de trajet restant des 30 min initialement allouée
        // Ex : Si l'utilisateur a fait 22 minutes de trottinettes, décompter 22 + 8 min supplémentaires car un trajet = 30 min
        // Envoyer la position GPs une dernière fois 
        // Arrêter la fonction update()
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