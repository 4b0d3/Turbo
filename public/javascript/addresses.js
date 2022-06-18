const HOST = document.documentElement.dataset.host;

function toggleAddAddressForm(el) {
    const form = document.getElementById("form-address-add");

    el.children[0].classList.toggle("displayNone");
    el.children[1].classList.toggle("displayNone");
    form.classList.toggle("displayNone");
}

function changeFavAddress(id) {
    const request = new XMLHttpRequest();
    
    request.onreadystatechange = function() {
        if(request.readyState == 4){
            console.log(request.response);
            // if(request.responseText != "null" && parseInt(request.responseText) != NaN) {
            //     if(parseInt(request.responseText) <= 0) {
            //         element.parentElement.parentElement.remove();
            //     } else {
            //         element.parentElement.children[1].textContent = request.responseText
            //         const prixUnite = element.parentElement.parentElement.children[2].textContent;
            //         element.parentElement.parentElement.children[4].textContent = request.responseText * prixUnite;
            //     }
            // }
        }
    }
    
    request.open("GET", HOST + "ajax/address/?action=changeFavAddress&id=" + id);
    
    request.send();
}