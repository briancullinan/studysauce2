$(document).ready(function () {

    var body = $('body');

    body.on('click', '#calculator .term-row > *:not(.term-editor)', function () {
        var calc = body.find('#calculator'),
            row = $(this).parents('.term-row');
        if(!row.is('selected')) {
            calc.find('.selected').removeClass('selected');
            row.addClass('selected');
        }
    });

    body.on('click', '#calculator .class-row > *:not(.grade-editor):not(.hours)', function () {
        var row = $(this).parents('.class-row');
        row.toggleClass('selected');
    });


});