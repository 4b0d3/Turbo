<?php 

namespace App\Controllers;

use App\Models\Cart;
use App\Models\Products;
use App\Models\Scooters;

class ShopController extends BaseController 
{
    public function getAll(array $data = []) 
    {
        $page = 0;
        $perPage = 10;

        if(isset($_GET["page"]) && !empty($_GET["page"])) {
            $page = intval($_GET["page"]) < 0 ? 0 : intval($_GET["page"]);
        }

        $products = Products::getAll($page, $perPage);
        $data["content"]["products"] = $products;


        $this->display("shop/shop.html.twig", $data);
    }

    public function getProduct(array $data = [])
    {
        $productId = $this->match["params"]["id"] ?? null;
        $data["product"] = Products::get($productId);

        if(!$data["product"]) {
            header("Location:" . HOST . "shop/?boxMsgs=Erreur;error;Produit non trouvé.");
            return;
        }
        $this->display("shop/product.html.twig", $data);
    }

    public function addProduct() 
    {
        $productId = $this->match["params"]["id"] ?? null;
        $exists = Products::get($productId);
        
        if(!$exists) return header("Location:" . HOST . "shop/?boxMsgs=Erreur;error;Produit non trouvé.");
        Cart::addProductOne($productId);

        header("Location: " . $this->router->generate("product", ["lang" => $this->lang, "id" => $productId]));
    }

    public function deleteProduct() 
    {
        $productId = $this->match["params"]["id"] ?? null;
        $exists = Products::get($productId);
        
        if(!$exists) return header("Location:" . HOST . "shop/?boxMsgs=Erreur;error;Produit non trouvé.");
        Cart::delProductOne($productId);

        header("Location: " . $this->router->generate("product", ["lang" => $this->lang, "id" => $productId]));
    }

    public function getCart(array $data = [])
    {
        $data["products"] = Cart::getAllProducts();

        $this->display("shop/cart.html.twig", $data);
    }
}