window.onload = init;




function init() {
    console.log("admin.js loaded");
    var menu = document.getElementById("top-menu");
    for (var i = 0; i < menu.children.length; i++) {
        menu.children[i].addEventListener("click", function() {
            var href = this.getAttribute("href");
            var id = href.substring(1);
            update_dashbord(id);
        });
    }
    var id = window.location.hash.substring(1);
    if(id != ""){
        update_dashbord(id);
    }else{
        update_dashbord("payment-log");
    }
} // end of init



function update_dashbord(id){
    console.log(id);
    var dashbord_contents = document.getElementsByClassName("dashbord-content");
    for (var i = 0; i < dashbord_contents.length; i++) {
        dashbord_contents[i].classList.add("hidden");
    }
    var dashbord = document.getElementById(id);
    dashbord.classList.remove("hidden");

}