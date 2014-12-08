$(document).ready(function () {

    var pan = null;

    $('body').on('show', '.page-top', function () {
        $('.scr h3').textfill({widthOnly: true});
    });

    $(window).resize(function () {
        $('.scr h3').textfill({widthOnly: true});
        $(this).trigger('scroll');
    });

    $(window).scroll(function () {
        if(pan != null)
            clearTimeout(pan);
        pan = setTimeout(function () {
            var video = $('.landing-home .video');
            var percent = $(this).scrollTop() / video.height();
            video.stop().animate({'background-position-y': 20 * percent - 50}, 100);
        }, 10);
    });
});