$(document).ready(function () {

    var deadlines = $('#deadlines');

    deadlines.on('click', '.deadline-row a[href="#edit-deadline"]', function (evt) {
        evt.preventDefault();
        var that = $(this),
            row = that.parents('.deadline-row');
        row.removeClass('read-only').addClass('edit');
    });

});