document.addEventListener("DOMContentLoaded",(
    function() {
        document.querySelectorAll(".confirm-action").forEach((function(e){
            e.addEventListener("click",(function(t){
                t.preventDefault();
                document.querySelector("#modal-confirm-button").addEventListener("click",(function(){
                    location.replace(e.getAttribute("href"));
                }));
            }));
        }));
    }
));