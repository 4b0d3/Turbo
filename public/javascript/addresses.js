function toggleAddAddressForm(el) {
    const form = document.getElementById("form-address-add");

    el.children[0].classList.toggle("displayNone");
    el.children[1].classList.toggle("displayNone");
    form.classList.toggle("displayNone");
}