let boxMsgs = document.querySelectorAll("div.box-msg > button");

boxMsgs.forEach(boxMsg => {
    console.log(boxMsg);
    boxMsg.addEventListener("click", function () {
        this.parentElement.remove();
    })
});

