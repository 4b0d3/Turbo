if (typeof HOST === 'undefined') {
    const HOST = document.documentElement.dataset.host;
}

function toggleAddAddressForm(el) {
    const form = document.getElementById("form-address-add");

    el.children[0].classList.toggle("displayNone");
    el.children[1].classList.toggle("displayNone");
    form.classList.toggle("displayNone");
}

function changeFavAddress(el, id) {
    const request = new XMLHttpRequest();
    
    request.onreadystatechange = function() {
        if(request.readyState == 4){
            const radio = el.querySelector("input");
            console.log(radio);
            radio.checked = true;
        }
    }
    
    request.open("GET", HOST + "ajax/address/?action=changeFavAddress&id=" + id);
    
    request.send();
}