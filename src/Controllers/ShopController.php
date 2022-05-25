<?php 

namespace App\Controllers;

use App\Models\Cart;
use App\Models\Products;
use App\Models\Scooters;
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

        $data["cart"]["products"] = Cart::getAllProducts();

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

        $data["cart"]["products"] = Cart::getAllProducts();

        $this->display("shop/cart.html.twig", $data);
    }

    public function getAllSubcriptions(array $data = [])
    {
        $data["cart"]["products"] = Cart::getAllProducts();
        $this->display("shop/subscriptions.html.twig", $data);
    }

    public function addSubscription()
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
        if($this->user->get("sub") != 0) {
            header("Location: " . $this->urls["BASEURL"] . "subscriptions/");
            return;
        }



        // TODO rediriger vers page de paiement puis red
        // header("Location: " . $this->urls["BASEURL"] . "subscriptions/pay/");
        // return;

        // Redirect vers la page de paiement 
        // ... Attente
        // En fonction du code de retour de la page 
        // Paiement pas passé -> Page d'erreur indiquant erreur de paiement et proposition de redirection vers la page des abonnements
        // Paiement passé -> Changer la valeur timeRemaining en fonction de l'abonnement et afficher une 
        // page de confirmation de paiement à l'utilisateur 

        Users::changeSub($_POST["sub"],  $this->user->get("id")); // TODO si le paiement passe
    }
}