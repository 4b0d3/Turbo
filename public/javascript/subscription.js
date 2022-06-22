function changeMensuelSub (idToChange) {
    const mensuels = document.querySelectorAll(".mensuel");

    mensuels.forEach(mensuel => {
        mensuel.classList.add("displayNone");
    });

    const el = document.getElementById(idToChange);
    el.classList.remove("displayNone");
}