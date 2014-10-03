$(document).ready(function () {
    var course = $('.course1');
    course.on('click', 'a[href="#submit-quiz"]', function () {
        course.addClass('right');
    });
});


