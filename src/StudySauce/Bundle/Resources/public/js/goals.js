$(document).ready(function () {

    var goals = $('#goals');

    goals.on('click', '.goal-row a[href="#goal-edit"]', function (evt) {
        evt.preventDefault();
        var that = $(this),
            row = that.parents('.goal-row');
        row.removeClass('read-only').addClass('edit');
    });

    goals.on('click', 'a[href="#save-goal"]', function (evt) {
        evt.preventDefault();
        var goalRows = [];
        goals.find('.goal-row.edit.valid:visible, .class-row.valid.edit:visible').each(function () {
            var row = $(this);
            goalRows[goalRows.length] = {
                type: row.find('.behavior, .milestone, .outcome').attr('class'),
                value: row.find('.behavior select, .milestone select, .outcome select').val(),
                reward: row.find('.reward textarea').val()
            };
        });

        $.ajax({
            url: window.callbackPaths['update_schedule'],
            type: 'POST',
            dataType: 'json',
            data: {
                goals: goalRows,
                csrf_token: goals.find('input[name="csrf_token"]').val()
            },
            success: function (data) {
                goals.find('input[name="csrf_token"]').val(data.csrf_token);

                $('.goal-row').replaceWith($(data.goals).find('.goal-row'));

                $(data.goals).find('.schedule.other .class-row')
                    .appendTo($('.schedule.other'));

                goals.find('.goal-row').planFunc();
            },
            error: function () {
            }
        });
    });

});