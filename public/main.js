$(document).ready(function() {
    $("body").css({ "overflow-x": "hidden" });
    $(".alert").delay(3000).fadeOut(1000);
    document.body.addEventListener('touchmove', function(e) { e.preventDefault(); });
    if ($(window).width() <= 1000) {
        $(".log_svg").hide();
        $(".btn2").show();
    } else {

        $(".log_svg").show();
        $(".btn2").hide();
    }
    if ($(window).width() <= 800) {
        $(".menu-big").hide();
        $(".menu-butt").show();


    } else {
        $(".firstel h1").hide(0);
        $(".firstel p").hide(0);
        $(".secondel h1").hide(0);
        $(".secondel p").hide(0);
        $(".tak").hide(0);

    }
    $("#exit").click(function() {
        $(".menu-small").fadeOut(1000);
    })
    setInterval(function() {
        if ($(window).width() <= 1000) {
            $(".log_svg").hide();
            $(".btn2").show();
        } else {

            $(".log_svg").show();
            $(".btn2").hide();
        }
        if ($(window).width() <= 1000) {

            $(".menu-big").hide();
            $(".menu-butt").show();

        } else {

            $(".menu-big").show();
            $(".menu-butt").hide();
        }
    }, 20)
    $(".menu-butt").click(function() {
        $(".menu-small").fadeIn(1000);
    })
    $(document).keyup(function(e) {
        if (e.key === "Escape") {
            $(".menu-small").fadeOut(1000);
        }
    });
    var toggled = 0;
    $("#toggle").click(function() {
        if (toggled == 0) {
            $("#dropdownmenu").slideDown(500);
            toggled = 1;
        } else {
            $("#dropdownmenu").slideUp(500);
            toggled = 0;
        }
    });
    var k = 0;
    $(window).scroll(function() {
        var top_of_element = $(".first").offset().top;
        var bottom_of_element = $(".first").offset().top + $(".first").outerHeight();
        var bottom_of_screen = $(window).scrollTop() + $(window).innerHeight();
        var top_of_screen = $(window).scrollTop();
        var top_of_element2 = $(".second").offset().top;
        var bottom_of_element2 = $(".second").offset().top + $(".second").outerHeight();
        var bottom_of_screen2 = $(window).scrollTop() + $(window).innerHeight();
        var top_of_screen2 = $(window).scrollTop();
        if ((bottom_of_screen2 > top_of_element2) && (top_of_screen2 < bottom_of_element2)) {
            $(".secondel h1").delay(400).slideDown(1000);
            $(".secondel p").delay(400).slideDown(1000);
            $(".tak").delay(200).slideDown(1000);

        }
        if ((bottom_of_screen > top_of_element) && (top_of_screen < bottom_of_element)) {
            $(".firstel h1").delay(200).slideDown(1000);
            $(".firstel p").delay(200).slideDown(1000);

            if (k == 0) {
                k = 1;
                var counter = 0;
                $('.counter').text(counter + "+");
                setTimeout(function() {
                    setInterval(function() {
                        if (counter < 10) {
                            counter++;
                        }

                        $('.counter').text(counter + "+");
                    }, 100)
                }, 600);



            }
        }
    });
})