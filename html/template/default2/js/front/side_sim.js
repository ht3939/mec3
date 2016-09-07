jQuery(function() {
    $(window).scroll(function () {
        var s = $(this).scrollTop();
        var m = 150;
        if(s > m) {
            $("#side_simlation").fadeIn('slow');
        } else if(s < m) {
            $("#side_simlation").fadeOut('slow');
        }
    });
});
