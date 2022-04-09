<?php

class Home {
    public $test = 1;

    public function render() :array 
    {
        $data = [
                "templateName" => "products.html.twig",
                "title" => "Products",
                "lang" => "en",
                "headerTitle" => "Checkout products",
                "articles" => [
                    ["title" => "Casque", "href" => "https://localhost:8000/shop/casque"],
                    ["title" => "trot", "href" => "https://localhost:8000/shop/trot"]]
            ];
        
        return $data;
    } 
}