$(document).ready(function () {
    var body = $('body');
    body.on('click', '.course1 a[href="#submit-quiz"]', function () {
        var course = $(this).parents('.course1');
        course.addClass('right');
    });
});


