var trigger = document.getElementById("trigger");
var clicked = 0;
trigger.addEventListener('click',function(event){
    if(!clicked){
        var show = document.getElementById("show");
        show.setAttribute('class','side show');
        trigger.setAttribute('class','card sh');
        clicked=1;
    }else{
        clicked=0;
        var show = document.getElementById("show");
        show.setAttribute('class','side');
        trigger.setAttribute('class','card');
    }
    
})