<?php 

namespace App\Controllers;

use App\Models\Cart;
use App\Models\Products;
use App\Models\Scooters;
use App\Models\Subscriptions;
use App\Models\Users;

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
        isset($_GET["showCart"]) ? $data["cart"]["show"] = true : false;
        $data["cart"]["products"] = Cart::getAllProducts();


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

        isset($_GET["showCart"]) ? $data["cart"]["show"] = true : false;
        $data["cart"]["products"] = Cart::getAllProducts();

        $this->display("shop/product.html.twig", $data);
    }

    public function addProduct() 
    {
        $productId = $this->match["params"]["id"] ?? null;
        $exists = Products::get($productId);
        
        if(!$exists) return header("Location:" . HOST . "shop/?boxMsgs=Erreur;error;Produit non trouvé.");
        Cart::addProductOne($productId);

        header("Location: " . $this->router->generate("product", ["lang" => $this->lang, "id" => $productId]) . "?showCart=true");
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

        $data["cart"]["info"] = Cart::getCartInfo();
        $data["cart"]["products"] = Cart::getAllProducts();
        
        $this->display("shop/cart.html.twig", $data);
    }

    public function getAllSubcriptions(array $data = [])
    {
        $data["cart"]["products"] = Cart::getAllProducts();
        $this->display("shop/subscriptions.html.twig", $data);
    }

    public function addSubscription(array $data = [])
    {
        if($this->user->isAnonymous()) {
            header("Location:" . $this->urls["BASEURL"] . "login/?r=subscriptions/");
            return;
        }

        $acceptedForfeits = [1,2,3,4];
        if(!isset($_POST["sub"]) || !in_array($_POST["sub"], $acceptedForfeits) ) {
            header("Location: " . $this->urls["BASEURL"] . "subscriptions/");
            return;
        }

        // Si l'utilisateur a déjà un abonnement alors retour vers le catalogue des abonnements
        // IMPLEM : Message d'erreur indiquant qu'il faut annuler l'abonnement en cours 
        // if($this->user->get("sub") != 0) {
        //     header("Location: " . $this->urls["BASEURL"] . "subscriptions/");
        //     return;
        // }
        
        // dump($_REQUEST);
        // die();

        $sub = Subscriptions::get($_POST["sub"]);
        
        \Stripe\Stripe::setApiKey($_ENV["STRIPE_API_PRIVATE"]);
        $price = intval(floatval($sub["price"])*100);
        $data["session"] = \Stripe\Checkout\Session::create([
        'payment_method_types' => ['card'],
        'line_items' => [[
            'price_data' => [
            'currency' => 'eur',
            'product_data' => [
                'name' => $sub["title"],
            ],
            'unit_amount' => $price,
            ],
            'quantity' => 1,
        ]],
        'mode' => 'payment',
        'success_url' => $this->urls["BASEURL"] . 'my-account/subscriptions/',
        'cancel_url' => $this->urls["BASEURL"] . 'subscriptions/',
        ]);

        $data["id"] = $data["session"]->id;
        // TODO si le paiement passe
        Users::changeSub($_POST["sub"],  $this->user->get("id")); // TODO si le paiement passe
        $data["stripe"]["public"] = $_ENV["STRIPE_API_PUBLIC"];
        
        $this->display("shop/pay.html.twig", $data);
    }

    public function getChooseShippment(array $data = [])
    {
        if($this->user->isAnonymous()) {
            header("Location:" . $this->urls["BASEURL"] . "login/?r=subscriptions/");
            return;
        }

        $data["addresses"] = Users::getAllAddresses($this->user->get("id"));

        $this->display("shop/shippment.html.twig", $data);
    }
}