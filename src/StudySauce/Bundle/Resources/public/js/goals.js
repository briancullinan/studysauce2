$(document).ready(function () {

    var goals = $('#goals');
    var goalsFunc = function () {
        jQuery(this).each(function (i, r) {
            var row = $(this).closest('.goal-row'),
                valid = true;
            if(row.find('select:visible').val() == '_none' ||
                row.find('.reward textarea').val().trim() == '')
                valid = false;
            if(!valid)
                row.removeClass('valid').addClass('invalid');
            else
                row.removeClass('invalid').addClass('valid');
        });
        if(goals.find('.goal-row.valid.edit:visible, .goal-row.edit.valid:visible').length > 0)
            goals.find('.form-actions').removeClass('invalid').addClass('valid');
        else
            goals.find('.form-actions').removeClass('valid').addClass('invalid');
    };

    goals.on('click', 'a[href="#claim"]', function (evt) {
        evt.preventDefault();
        $('#claim').modal();
    });

    goals.on('click', '.goal-row a[href="#goal-edit"]', function (evt) {
        evt.preventDefault();
        var that = $(this),
            row = that.parents('.goal-row');
        goalsFunc.apply(row.removeClass('read-only').addClass('edit'));
    });

    goals.on('change', '.goal-row select, .goal-row textarea', function () {
        goalsFunc.apply(jQuery(this).parents('.goal-row'));
    });
    goals.on('keyup', '.goal-row textarea', function () {
        goalsFunc.apply(jQuery(this).parents('.goal-row'));
    });

    goals.on('click', 'a[href="#save-goal"]', function (evt) {
        evt.preventDefault();
        if(goals.find('.form-actions').is('.invalid'))
            return;
        goals.find('.form-actions').removeClass('valid').addClass('invalid');

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
            url: window.callbackPaths['update_goals'],
            type: 'POST',
            dataType: 'text',
            data: {
                goals: goalRows,
                csrf_token: goals.find('input[name="csrf_token"]').val()
            },
            success: function (data) {
                var response = $(data);
                goals.find('input[name="csrf_token"]').val(response.find('input[name="csrf_token"]').val());

                goals.find('.goal-row').remove();
                $(response).find('.goal-row').insertAfter(goals.find('header'));
                // TODO: make new rows fade in to place

                goalsFunc.apply(goals.find('.goal-row'));
            },
            error: function () {
            }
        });
    });

    goalsFunc.apply(goals.find('.goal-row'));
});