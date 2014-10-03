$(document).ready(function () {

    setInterval(function () {
        var cI = $('.flip-clock-wrapper li.active'),
            i = cI.length > 0 ? cI.removeClass('active').first().index() + 1 : 0;
        $('.flip-clock-wrapper li:nth-child(' + (i+1) + ')').addClass('active');
    }, 3000);
});