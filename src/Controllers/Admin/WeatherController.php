<?php

namespace App\Controllers\Admin;
use App\Controllers\BaseController;
use App\Models\Weather;

class WeatherController extends BaseController
{
    public function getAll(array $data = [])
    {
        if(!$this->checkAdminAccess()) return;

        $data["weathers"] = Weather::getAll();
        $this->display("admin/weather/weather.html.twig", $data);
    }

    public function postReload(array $data = []){
        if(!$this->checkAdminAccess()) return;

    }

}