$(document).ready(function () {
    $('body').on('show', '.page-top', function () {
        $('.scr h3').textfill({widthOnly: true});
    });

    $(window).resize(function () {
        $('.scr h3').textfill({widthOnly: true});
    });
});