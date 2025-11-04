//menu.js
document.addEventListener('DOMContentLoaded', function() {
    let nav=document.querySelector("#navArea");
    let btn=document.querySelector(".toggle");
    let mask=document.querySelector("#mask");
    
    if(btn && nav && mask){
        btn.onclick= function() {
            nav.classList.toggle("open");
        };
        
        mask.onclick= function() {
            nav.classList.toggle("open");
        };
    }
});