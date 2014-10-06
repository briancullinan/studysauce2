$(document).ready(function () {
    var body = $('body');
    body.on('click', '.course1 a[href="#submit-quiz"]', function (evt) {
        evt.preventDefault();
        var course = $(this).parents('.course1');
        course.addClass('right');
    });

    body.on('change', '#lesson1-step4 textarea', function () {
        if($(this).val().trim() != '')
            body.find('.highlighted-link').removeClass('invalid').addClass('valid');
        else
            body.find('.highlighted-link').removeClass('valid').addClass('invalid');
    });

    body.on('keyup', '#lesson1-step4 textarea', function () {
        if($(this).val().trim() != '')
            body.find('.highlighted-link').removeClass('invalid').addClass('valid');
        else
            body.find('.highlighted-link').removeClass('valid').addClass('invalid');
    });
});


