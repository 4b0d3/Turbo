
function delFromCart(id) {    
    const request = new XMLHttpRequest();
    
    request.onreadystatechange = function() {
        if(request.readyState == 4){
            refreshCart();
        }
    }
    
    // TODO
    request.open("GET", "http://localhost/ESGI/ESGI2/Projet%20Annuelp/Turbo/public/javascript/scripts/addCartElement.php?action=del&productId="+id);
    
    request.send();
}

function addToCart(id) {        
    const request = new XMLHttpRequest();
    
    request.onreadystatechange = function() {
        if(request.readyState == 4){
            refreshCart();
        }
    }
    
    // TODO
    request.open("GET", "http://localhost/ESGI/ESGI2/Projet%20Annuelp/Turbo/public/javascript/scripts/addCartElement.php?action=add&productId="+id);
    
    request.send();
}

function refreshCart() {
    let btnCart = document.getElementById("btn-cart");
    
    let userId = btnCart.getAttribute("data-userId");

    let cartContent = document.getElementById("cart-content");
    cartContent.innerHTML = "";

    const request = new XMLHttpRequest();
    
    request.onreadystatechange = function() {
        if(request.readyState == 4){
            let elements = JSON.parse(request.responseText);
            if(elements.length <= 0) {

            } else {
                for (let element of elements.entries()) {
                    element = element[1];
                    let tr = document.createElement("tr");
                    let tdName = document.createElement("td");
                    tdName.textContent = element.name;
                    tr.append(tdName)

                    let tdPrice = document.createElement("td");
                    tdPrice.textContent = element.price;
                    tr.append(tdPrice)

                    let buttonDel = document.createElement("button");
                    buttonDel.textContent = "-";
                    buttonDel.addEventListener("click", function () { delFromCart(element.id) })
                    let tdQuantity = document.createElement("td");
                    tdQuantity.textContent = element.quantity;
                    let buttonAdd = document.createElement("button");
                    buttonAdd.textContent = "+";
                    buttonAdd.addEventListener("click", function () { addToCart(element.id) })
                    tr.append(buttonDel);
                    tr.append(tdQuantity);
                    tr.append(buttonAdd);

                    cartContent.append(tr);
                }
            }
        }
    }
    
    // TODO
    request.open("GET", "http://localhost/ESGI/ESGI2/Projet%20Annuelp/Turbo/public/javascript/scripts/getCartElements.php?userId="+userId);
    
    request.send();
}

let btnCart = document.getElementById("btn-cart");

btnCart.addEventListener("click", function () {
    let cart = document.getElementById("cart");
    cart.classList.toggle("hide");
})

refreshCart();

