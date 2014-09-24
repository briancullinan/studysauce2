jQuery(document).ready(function () {

    var deadlines = jQuery('#deadlines');

    deadlines.on('click', '.deadline-row a[href="#edit-deadline"]', function (evt) {
        evt.preventDefault();
        var that = jQuery(this),
            row = that.parents('.deadline-row');
        row.removeClass('read-only').addClass('edit');
    });

});