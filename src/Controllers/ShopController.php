<?php 

namespace App\Controllers;

use App\Models\Cart;

use App\Models\Commands;

use App\Models\Partners;

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
        $data["subscriptions"] = Subscriptions::getAll();
        $this->display("shop/subscriptions.html.twig", $data);
    }

    public function addSubscription(array $data = [])
    {
        if($this->user->isAnonymous()) {
            header("Location:" . $this->urls["BASEURL"] . "login/?r=subscriptions/");
            return;
        }

        $acceptedForfeits = array_column(Subscriptions::getAll(), "id");

        if(!isset($_POST["sub"]) || !in_array($_POST["sub"], $acceptedForfeits) ) {
            header("Location: " . $this->urls["BASEURL"] . "subscriptions/");
            return;
        }

        if(!empty($this->user->get("sub"))) {
            header("Location: " . $this->urls["BASEURL"] . "my-account/subscriptions/");
            return;
        }

        $sub = Subscriptions::get($_POST["sub"]);
        
        \Stripe\Stripe::setApiKey($_ENV["STRIPE_API_PRIVATE"]);
        $price = floatval($sub["price"])*100;
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
        
        Users::changeSub($_POST["sub"],  $this->user->get("id")); 
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
        $data["cart"]["products"] = Cart::getAllProducts();

        $this->display("shop/shippment.html.twig", $data);
    }

    public function getPay(array $data = []) {
        if($this->user->isAnonymous()) {
            header("Location:" . $this->urls["BASEURL"] . "login/?r=cart/");
            return;
        }
        $acceptedAddresses = array_column(Users::getAllAddresses($this->user->get("id")), "id");
        
        if(!isset($_GET["idAddress"]) || !in_array($_GET["idAddress"], $acceptedAddresses) ) {
            header("Location: " . $this->urls["BASEURL"] . "cart/");
            return;
        }

        $products = Cart::getAllProducts();
        $lineItems = [];
        $toSendProducts="";


        foreach($products as $product) {
            $lineItem["price_data"] = ['currency' => 'eur', 'product_data' => ['name' => $product["name"]], 'unit_amount' => floatval($product["price"])*100];
            $lineItem["quantity"] = $product["quantity"];
            array_push($lineItems, $lineItem);
            $toSendProducts = $toSendProducts . $product['id'] . ":" . $product["quantity"] . ";";
        }
        
        \Stripe\Stripe::setApiKey($_ENV["STRIPE_API_PRIVATE"]);
        $data["session"] = \Stripe\Checkout\Session::create([
        'payment_method_types' => ['card'],
        'line_items' => $lineItems,
        'mode' => 'payment',
        'success_url' => $this->urls["BASEURL"] . 'command/success/?idAddress=' . $_GET["idAddress"] . "&products=" . $toSendProducts,
        'cancel_url' => $this->urls["BASEURL"] . 'cart/',
        ]);

        $data["id"] = $data["session"]->id;
        $data["stripe"]["public"] = $_ENV["STRIPE_API_PUBLIC"];
        
        $this->display("shop/pay.html.twig", $data);
    }

    public function getSuccess(array $data = []) {
        $idAddress = $_GET["idAddress"];
        $products = $_GET["products"];
        $cartInfo = Cart::getCartInfo();
        $commandInfo = [
            "idUser" => $this->user->get("id"),
            "idAddress" => $idAddress,
            "total" => $cartInfo["total"],
            "products" => $products,
        ];

        Commands::add($commandInfo);
        header("Location: " . $this->urls["BASEURL"] . "my-account/orders/");
    }

    public function getAllPartners(array $data = []){
        $data["cart"]["products"] = Cart::getAllProducts();
        $data["partners"] = Partners::getAll(1);
        $this->display("shop/partners.html.twig", $data);
    }

    public function addOffer(){
        $partnerId = $this->match["params"]["id"] ?? null;
        $exists = Partners::get($partnerId);
        $userId = $this->user->get("id");
        
        if(!$exists) return header("Location:" . $this->urls["BASEURL"] . "partners/?boxMsgs=Erreur;error;Partenariat non trouvé.");
        $prix = Partners::getPrice($partnerId);
        $userTurboz = Partners::getTurboz($userId);
        // dump($prix);
        // dump($userTurboz);
        // die;


        if ($userTurboz["turboz"] > $prix["price"] ){
            $result = $userTurboz["turboz"] - $prix["price"];
            $req = Partners::buy($userId, $result);
            if($req){
                $data["code"] = Partners::getCode($partnerId);
                $this->display("shop/partnersCode.html.twig", $data);
            }
        } else{
            header("Location:" . $this->urls["BASEURL"] . "partners/?boxMsgs=Erreur;error;Solde non suffisant.");
        }

        
    
    }
    
}