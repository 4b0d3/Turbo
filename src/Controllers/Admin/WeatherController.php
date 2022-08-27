<?php

namespace App\Controllers\Admin;
use App\Controllers\BaseController;
use App\Models\Weather;

class WeatherController extends BaseController
{
    public function getAll(array $data = [])
    {
        if(!$this->checkAdminAccess()) return;

        $data["Weathers"] = Weather::getAll();
        $this->display("admin/weather/weather.html.twig", $data);
    }

    public function getAdd(array $data = []){
        if(!$this->checkAdminAccess()) return;

        $this->display("admin/weather/weatherAdd.html.twig", $data);

    }

}