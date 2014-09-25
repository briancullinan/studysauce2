$(document).ready(function () {

    var goals = $('#goals');

    goals.on('click', '.goal-row a[href="#goal-edit"]', function (evt) {
        evt.preventDefault();
        var that = $(this),
            row = that.parents('.goal-row');
        row.removeClass('read-only').addClass('edit');
    });

});