jQuery(document).ready(function () {

    var schedule = jQuery('#schedule');

    schedule.on('click', '.class-row a[href="#edit-class"]', function (evt) {
        evt.preventDefault();
        var that = jQuery(this),
            row = that.parents('.class-row');
        row.removeClass('read-only').addClass('edit');
    });

});