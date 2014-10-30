$(document).ready(function () {
    $('body').on('loaded', '.page-top', function () {
        $('.scr h3').textfill({widthOnly: true});
    });

    $(window).resize(function () {
        $('.scr h3').textfill({widthOnly: true});
    });
});