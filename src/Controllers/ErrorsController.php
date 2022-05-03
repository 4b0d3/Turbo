<?php

namespace App\Controllers;

class ErrorsController extends BaseController {
    public function get() {
        $data = $this->getData($this->match["error"] ?? 500);
        $this->display("error.html.twig", $data);
    }

    public function getData(int $code) :array
    {
        $data["code"] = $code;

        switch($code) {
            case 404:
                $data["status"] = "Not found";
                $data["message"] = "Woops. Looks like this page doesn't exist.";
                break;

            case 500:
                $data["status"] = "Server problem;";
                $data["message"] = "Woops. Looks like the server has encountered a problem.";
                break;

            default:
                $data["status"] = "not found";
                $data["message"] = "Woops. Looks like this page doesn't exist.";
        }

        return $data;
    }
}