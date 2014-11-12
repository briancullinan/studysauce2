$(document).ready(function () {

    var body = $('body');

    function datesFunc() {
        var deadlines = $('#deadlines');
        jQuery(this).each(function () {
            var that = jQuery(this),
                error = false;
            if(that.find('select').val() == '')
                error = true;
            if(that.find('.assignment input').val().trim() == '')
                error = true;
            if(that.find('.reminder input:checked').length == 0)
                error = true;
            if(that.find('.due-date input').val().trim() == '')
                error = true;

            if(error)
                that.removeClass('valid').addClass('invalid');
            else
                that.removeClass('invalid').addClass('valid');

            if(that.find('.class-name select').val() == 'Nonacademic')
                that.find('.percent').css('visibility', 'hidden');
            else
                that.find('.percent').css('visibility', 'visible');

            that.find('.due-date input').datepicker({
                minDate: 0,
                autoPopUp:'focus',
                changeMonth: true,
                changeYear: true,
                closeAtTop: false,
                dateFormat: 'mm/dd/yy',
                defaultDate:'0y',
                firstDay:0,
                fromTo:false,
                speed:'immediate',
                yearRange: '-3:+3'
            });
        });

        if(deadlines.find('.deadline-row.edit.valid').length == 0)
            deadlines.find('.form-actions').removeClass('valid').addClass('invalid');
        else
            deadlines.find('.form-actions').removeClass('invalid').addClass('valid');
    }

    body.on('show', '#deadlines', function () {
        // show empty
        if($(this).is('.empty'))
            $('#deadlines-empty').modal({
                backdrop: 'static',
                keyboard: false,
                modalOverflow: true
            });
        else {
            $('#deadlines-empty').modal('hide');
            datesFunc.apply($(this).find('.deadline-row'));
        }
    });
    body.find('#deadlines:visible').trigger('show');
    body.on('click', '#deadlines .deadline-row a[href="#edit-deadline"]', function (evt) {
        var deadlines = $('#deadlines');
        evt.preventDefault();
        var that = $(this),
            row = that.parents('.deadline-row');
        datesFunc.apply(row.removeClass('read-only').addClass('edit'));
        deadlines.find('.highlighted-link').last().detach().insertAfter(row);
    });

    function updateDeadlines(response)
    {
        var deadlines = $('#deadlines');
        // update key dates list
        deadlines.find('.deadline-row,.head').remove();
        response.find('.deadline-row,.head').insertAfter(deadlines.find('header'));
        deadlines.find('.highlighted-link').last().detach().insertAfter(deadlines.find('.deadline-row').last());
        if(deadlines.find('.deadline-row:not(.first):visible,.head:visible').length == 0) {
            deadlines.find('.deadline-row.first').detach().insertBefore(deadlines.find('.form-actions').first());
            deadlines.find('.sort-by,header').hide();
        }
        else {
            deadlines.find('.sort-by,header').show();
        }
    }

    body.on('click', '#deadlines a[href="#save-deadline"]', function (evt) {
        var deadlines = $('#deadlines');
        evt.preventDefault();

        if(deadlines.find('.form-actions').is('.invalid'))
            return;
        deadlines.find('.form-actions').removeClass('valid').addClass('invalid');

        var dates = [];
        deadlines.find('.deadline-row.edit.valid, .deadline-row.valid.edit').each(function () {
            var row = $(this),
                deadlineId = (/deadline-id-([0-9]*)(\s|$)/ig).exec(row.attr('class'))[1],
                reminders = row.find('.reminder input:checked').map(function (i, x) {return $(x).val();}).get();
            dates[dates.length] = {
                eid: deadlineId,
                courseId: row.find('.class-name select').val(),
                assignment: row.find('.assignment input').val(),
                reminders: reminders.join(','),
                due: row.find('.due-date input').val(),
                percent: row.find('.percent').is(':visible') ? row.find('.percent input').val() : 0
            };
        });

        $.ajax({
            url: window.callbackPaths['update_deadlines'],
            type: 'POST',
            dataType: 'text',
            data: {
                dates: dates,
                csrf_token: deadlines.find('input[name="csrf_token"]').val()
            },
            success: function (data) {
                var response = $(data);
                deadlines.find('input[name="csrf_token"]').val(response.find('input[name="csrf_token"]').val());
                updateDeadlines(response);
            },
            error: function () {
            }
        });
    });

    body.on('scheduled', function () {
        var deadlines = $('#deadlines'),
            schedule = $('#schedule').find('.schedule:not(.other) .class-row'),
            options = schedule.map(function () {
                var row = $(this),
                    courseId = (/course-id-([0-9]*)(\s|$)/ig).exec(row.attr('class'))[1],
                    className = row.find('.class-name input').val();
                return '<option value="' + courseId + '">' + className + '</option>';
            });
        if(options.length == 0)
            deadlines.addClass('empty');
        else
            deadlines.removeClass('empty');
        deadlines.find('.class-name select').replaceWith('<select>' + options.join('') + '</select>');
    });
    body.on('click', '#deadlines .deadline-row:not(.edit) > div:not(.reminder)', function () {
        jQuery(this).parents('.deadline-row').toggleClass('selected');
    });
    body.on('change', '#deadlines .class-name select, #deadlines .reminder input, #deadlines .due-date input', function () {
        datesFunc.apply(jQuery(this).parents('.deadline-row'));
    });
    body.on('keyup', '#deadlines .assignment input, #deadlines .due-date input', function () {
        datesFunc.apply(jQuery(this).parents('.deadline-row'));
    });
    body.on('click', '#deadlines a[href="#add-deadline"]', function (evt) {
        var deadlines = $('#deadlines');
        evt.preventDefault();
        var newDeadline = deadlines.find('.deadline-row').first().clone()
            .removeClass('read-only hide').addClass('edit').insertBefore(deadlines.find('.form-actions').first());
        newDeadline.attr('class', newDeadline.attr('class').replace(/deadline-id-([0-9]*)(\s|$)/ig, ' deadline-id- '));
        newDeadline.find('.class-name select, .assignment input, .percent input').val('');
        newDeadline.find('.due-date input').removeClass('hasDatepicker').val('');
        newDeadline.find('.reminder input').removeAttr('checked').prop('checked', false);
        datesFunc.apply(newDeadline);
    });

    body.on('click', '#deadlines a[href="#remove-deadline"]', function (evt) {
        var deadlines = $('#deadlines');
        evt.preventDefault();
        var row = jQuery(this).parents('.deadline-row'),
            deadlineId = (/deadline-id-([0-9]*)(\s|$)/ig).exec(row.attr('class'))[1];
        $.ajax({
            url: window.callbackPaths['remove_deadlines'],
            type: 'POST',
            dataType: 'text',
            data: {
                csrf_token: deadlines.find('input[name="csrf_token"]').val(),
                remove: deadlineId
            },
            success: function (data) {
                var response = $(data);
                deadlines.find('input[name="csrf_token"]').val(response.find('input[name="csrf_token"]').val());
                updateDeadlines(response);
            }
        });
    });

    body.on('change', '#deadlines .sort-by .checkbox input', function () {
        var deadlines = $('#deadlines');
        if(jQuery(this).is(':checked'))
            deadlines.addClass('show-historic');
        else
            deadlines.removeClass('show-historic');
    });

    body.on('change', '#deadlines .sort-by input[type="radio"]', function () {
        var headings = {},
            that = jQuery(this),
            deadlines = $('#deadlines');
        deadlines.find('.head').each(function () {
            var head = jQuery(this);
            head.nextUntil('*:not(.deadline-row)').each(function () {
                var row = jQuery(this),
                    courseId = (/course-id-([0-9]+)(\s|$)/ig).exec(row.attr('class'))[1],
                    that = row.find('.class-name .read-only');
                // TODO: fix this to not rely on name
                if(typeof headings[courseId] == 'undefined')
                    headings[courseId] = row;
                else
                    headings[courseId] = jQuery.merge(headings[courseId], row);
                that.html(that.html().replace(courseId, head.text().trim()));
            });
        });
        var rows = [];
        // sort headings by class name
        if(that.val() == 'class')
        {
            var keys = [];
            for(var i = 0; i < window.classIds.length; i++)
                if(typeof headings[window.classIds[i]] != 'undefined')
                    keys[keys.length] = window.classIds[i];

            for(var k in headings)
                if(keys.indexOf(k) == -1)
                    keys[keys.length] = k;

            for(var j = 0; j < keys.length; j++)
            {
                var hidden = headings[keys[j]].filter('.deadline-row:not(.hide)').length == 0;
                rows = jQuery.merge(rows, jQuery.merge(jQuery('<div class="head ' + (hidden ? 'hide' : '') + '">' + keys[j] + '</div>'), headings[keys[j]].detach()));
            }
        }
        else
        {
            var keys2 = [];
            for(var h2 in headings)
                keys2[keys2.length] = Date.parse(h2);

            keys2.sort();
            var monthNames = [ "January", "February", "March", "April", "May", "June",
                "July", "August", "September", "October", "November", "December" ];

            for(var j2 = 0; j2 < keys2.length; j2++)
            {
                var d = new Date(keys2[j2]),
                    h = d.getDate() + ' ' + monthNames[d.getMonth()] + ' ' + d.getFullYear();
                var hidden2 = headings[h].filter('.deadline-row:not(.hide)').length == 0;
                rows = jQuery.merge(rows, jQuery.merge(jQuery('<div class="head ' + (hidden2 ? 'hide' : '') + '">' + d.getDate() + ' ' + monthNames[d.getMonth()] + ' <span>' + d.getFullYear() + '</span></div>'), headings[h].detach()));
            }
        }
        jQuery('.sort-by').nextAll('.head').remove();
        jQuery(rows).insertAfter(deadlines.find('.sort-by'));
        // reassign first row
        deadlines.find('.first').removeClass('first');
        deadlines.find('.deadline-row:not(.hide,#new-dates-row)').first().addClass('first');
        deadlines.find('.deadline-row.hide').first().addClass('first');
    });
});