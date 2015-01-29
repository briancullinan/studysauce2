$(document).ready(function () {

    var body = $('body');

    function planFunc() {
        var schedule = $('#schedule');
        jQuery(this).each(function () {
            var row = $(this).closest('.class-row');
            if(row.length == 0)
                return;
            if(row.find('.class-name input').val().trim() == '' &&
                row.find('.day-of-the-week input:not([value="Weekly"]):checked').length == 0 &&
                row.find('.start-date input').val().trim() == '' &&
                row.find('.end-date input').val().trim() == '')
                row.removeClass('invalid').addClass('valid blank');
            else if(row.find('.class-name input').val().trim() == '' ||
                (row.parent().not('.schedule.other') &&
                row.find('.day-of-the-week input:checked').length == 0) ||
                row.find('.start-time input').val().trim() == '' ||
                row.find('.end-time input').val().trim() == '' ||
                row.find('.start-date input').val().trim() == '' ||
                row.find('.end-date input').val().trim() == '')
                row.removeClass('valid blank').addClass('invalid');
            else
                row.removeClass('invalid blank').addClass('valid');

            row.find('.start-date input[type="text"], .end-date input[type="text"]')
                .datepicker({
                    showOtherMonths: true,
                    selectOtherMonths: true,
                    autoPopUp:'focus',
                    changeMonth: true,
                    changeYear: true,
                    closeAtTop: false,
                    dateFormat: 'mm/dd/y',
                    defaultDate:'0y',
                    firstDay:0,
                    fromTo:false,
                    speed:'immediate',
                    yearRange: '-3:+3'
                }).on('focus', function () {
                    setTimeout(function () {
                        $('#ui-datepicker-div').scrollintoview(DASHBOARD_MARGINS);
                    }, 50);
                });
            row.find('.start-time input[type="text"]:not(.is-timeEntry), .end-time input[type="text"]:not(.is-timeEntry)')
                .timeEntry({
                    defaultTime: new Date(0, 0, 0, 6, 0, 0),
                    ampmNames: ['AM', 'PM'],
                    ampmPrefix: ' ',
                    fromTo: false,
                    show24Hours: false,
                    showSeconds: false,
                    spinnerImage: '',
                    timeSteps: [1,1,"1"]
                })
                .on('keypress', function (event) {
                    var that = jQuery(this),
                        row = that.parents('.class-row'),
                        from = row.find('.start-time input[type="text"]').timeEntry('getTime'),
                        to = row.find('.end-time input[type="text"]').timeEntry('getTime');

                    if(that.data('processing'))
                        return;
                    that.data('processing', true);

                    var chr = String.fromCharCode(event.charCode === undefined ? event.keyCode : event.charCode);
                    if (chr < ' ') {
                        return;
                    }
                    var ampmSet = that.data('ampmSet') || false;
                    if(chr.toLowerCase() == 'a' || chr.toLowerCase() == 'p')
                        that.data('ampmSet', true);
                    else if (chr >= '0' && chr <= '9' && !ampmSet)
                    {
                        var time = that.timeEntry('getTime');
                        var hours = time.getHours();
                        var newTime = time;
                        if(hours < 7)
                            newTime = new Date(0, 0, 0, hours + 12, time.getMinutes(), 0);
                        // check the length in between to see if its longer than 12 hours
                        else if(hours >= 19)
                            newTime = new Date(0, 0, 0, hours - 12, time.getMinutes(), 0);

                        if((that.parents('.start-time') && to == null || newTime.getTime() < to.getTime()) ||
                            (that.parents('.end-time') && from == null || newTime.getTime() > from.getTime()))
                            that.timeEntry('setTime', newTime);
                    }

                    that.data('processing', false);
                });

            // check for invalid time entry
            var from = row.find('.start-time input').timeEntry('getTime'),
                to = row.find('.end-time input').timeEntry('getTime');
            if(from != null && to != null) {
                var length = (to.getTime() - from.getTime()) / 1000;
                if (length < 0)
                    length += 86400;
                // check if the length is less than 8 hours
                if (from.getTime() == to.getTime() || length > 8 * 60 * 60)
                    row.addClass('invalid-time');
                else
                    row.removeClass('invalid-time');
            }

            // check if there are any overlaps with the other rows
            var startDate = new Date(row.find('.start-date input').val());
            var endDate = new Date(row.find('.end-time input').val());
            var startTime = new Date('1/1/1970 ' + row.find('.start-time input').val().replace(/[ap]m$/i, '').replace(/12:/i, '00:'));
            if(row.find('.start-time input').val().match(/pm$/i) != null)
                startTime = startTime.addHours(12);
            var endTime = new Date('1/1/1970 ' + row.find('.end-time input').val().replace(/[ap]m$/i, '').replace(/12:/i, '00:'));
            if(row.find('.end-time input').val().match(/pm$/i) != null)
                endTime = endTime.addHours(12);
            var dotw = row.find('.day-of-the-week input:not([value="Weekly"]):checked').map(function (i, x) {return $(x).val();}).get();
            // reset overlaps tag to start
            var overlaps = row.is('.overlaps');
            row.removeClass('overlaps');
            schedule.find('.class-row').not(row).each(function () {
                var that = jQuery(this);
                // check if dates overlap
                var startDate2 = new Date(that.find('.start-date input').val());
                var endDate2 = new Date(that.find('.end-date input').val());
                if(isNaN(startDate.getTime()) || isNaN(endDate.getTime()) ||
                    isNaN(startDate2.getTime()) || isNaN(endDate2.getTime()) ||
                    startDate < endDate2 || endDate > startDate2)
                {
                    // check if weekdays overlap
                    var dotwOverlaps = false,
                        dotw2 = that.find('.day-of-the-week input:not([value="Weekly"]):checked').map(function (i, x) {return $(x).val();}).get();
                    for(var i in dotw)
                        if(dotw.hasOwnProperty(i) && dotw2.indexOf(dotw[i]) > -1)
                        {
                            dotwOverlaps = true;
                            break;
                        }
                    if(dotwOverlaps)
                    {
                        // check if times overlap
                        var startTime2 = new Date('1/1/1970 ' + that.find('.start-time input').val().replace(/[ap]m$/i, '').replace(/12:/i, '00:'));
                        if(that.find('.start-time input').val().match(/pm$/i) != null)
                            startTime2 = startTime2.addHours(12);
                        var endTime2 = new Date('1/1/1970 ' + that.find('.end-time input').val().replace(/[ap]m$/i, '').replace(/12:/i, '00:'));
                        if(that.find('.end-time input').val().match(/pm$/i) != null)
                            endTime2 = endTime2.addHours(12);
                        if(startTime < endTime2 && endTime > startTime2)
                        {
                            that.addClass('overlaps');
                            row.addClass('overlaps');
                        }
                    }
                }
            });

            // if it changed, remove other overlaps
            if(overlaps && !row.is('.overlaps'))
            {
                planFunc.apply(schedule.find('.class-row.overlaps'));
            }
        });

        /*if(window.location.pathname == '/schedule2' &&
            schedule.find('.class-row.edit.invalid:visible').length == 0 &&
            schedule.find('.class-row.overlaps:visible').length == 0 &&
            schedule.find('.class-row.invalid-time:visible').length == 0)
            schedule.removeClass('invalid-only').find('.form-actions').removeClass('invalid').addClass('valid');
        else if(window.location.pathname == '/schedule' &&
            schedule.find('.class-row.edit.invalid:visible').length == 0 &&
            schedule.find('.class-row.overlaps:visible').length == 0 &&
            schedule.find('.class-row.invalid-time:visible').length == 0 &&
            schedule.find('.class-row.edit.valid:visible').not('.blank').length > 0 &&
            schedule.find('.university input').val().trim() != '')
            schedule.removeClass('invalid-only').find('.form-actions').removeClass('invalid').addClass('valid');
        else */
        if(schedule.find('.class-row.edit.invalid:visible').length == 0 &&
            schedule.find('.class-row.overlaps:visible').length == 0 &&
            schedule.find('.class-row.invalid-time:visible').length == 0 &&
            schedule.find('.university input').val().trim() != '' &&
            (schedule.find('.class-row.edit.valid:visible').not('.blank').length > 0 ||
            (schedule.find('.class-row.valid').not('.blank').length > 0 &&
            schedule.find('.university input').val().trim() !=
            schedule.find('.university input').data('state'))))
            schedule.removeClass('invalid-only').find('.form-actions').removeClass('invalid').addClass('valid');
        else
            schedule.find('.form-actions').removeClass('valid').addClass('invalid');

        if(schedule.find('.class-row.overlaps:visible').length > 1)
            schedule.addClass('overlaps');
        else
            schedule.removeClass('overlaps');

        if(schedule.find('.class-row.invalid-time:visible').length > 0)
            schedule.addClass('invalid-time');
        else
            schedule.removeClass('invalid-time');
    }

    body.on('click', '#schedule a[href="#edit-class"]', function (evt) {
        evt.preventDefault();
        var that = $(this),
            row = that.parents('.class-row');
        planFunc.apply(row.removeClass('read-only').addClass('edit'));
        row.find('.start-time input[type="text"], .end-time input[type="text"]').trigger('change');
    });

    body.on('click', '#schedule a[href="#remove-class"]', function (evt) {
        var schedule = $('#schedule');
        evt.preventDefault();
        var row = jQuery(this).parents('.class-row'),
            courseId = (/course-id-([0-9]*)(\s|$)/ig).exec(row.attr('class'))[1];
        $.ajax({
            url: window.callbackPaths['remove_schedule'],
            type: 'POST',
            dataType: 'text',
            data: {
                remove: courseId,
                csrf_token: schedule.find('input[name="csrf_token"]').val()
            },
            success: function (data) {
                updateSchedule(data);
            }
        });
    });

    function updateSchedule(data)
    {
        var schedule = $('#schedule');
        var response = $(data);
        schedule.find('.university input').data('state', schedule.find('.university input').val().trim());
        schedule.find('input[name="csrf_token"]').val(response.find('input[name="csrf_token"]').val());
        // update class schedule
        $('.schedule .class-row').remove();
        response.find('.schedule:not(.other) .class-row')
            .prependTo($('.schedule:not(.other)'));

        response.find('.schedule.other .class-row')
            .prependTo($('.schedule.other'));

        planFunc.apply(schedule.find('.schedule .class-row'));
        schedule.scrollintoview(DASHBOARD_MARGINS);
        body.trigger('scheduled');
    }

    body.on('click', '#schedule a[href="#add-class"], #schedule a[href="#add-other"]', function (evt) {
        var schedule = $('#schedule');
        evt.preventDefault();
        var isOther = jQuery(this).is('[href="#add-other"]'),
            examples = ['HIST 101', 'CALC 120', 'MAT 200', 'PHY 110', 'BUS 300', 'ANT 350', 'GEO 400', 'BIO 250', 'CHM 180', 'PHIL 102', 'ENG 100'],
            otherExamples = ['Work', 'Practice', 'Gym', 'Meeting'],
            list = schedule.find('.schedule' + (isOther ? '.other' : ':not(.other)')),
            addClass = list.find('.class-row').first().clone().removeAttr('id')
                .removeClass('read-only').addClass('edit').insertBefore(list.find('.form-actions'));
        // reset fields for the new entry
        addClass.attr('class', addClass.attr('class').replace(/course-id-([0-9]*)(\s|$)/ig, ' course-id- '));
        addClass.find('.class-name input, .start-date input, .end-date input, .start-time input, .end-time input')
            .removeClass('is-timeEntry hasDatepicker').removeAttr('id').val('');
        addClass.find('.day-of-the-week input').removeAttr('checked').prop('checked', false);
        addClass.find('.class-name input').attr('placeholder', isOther
                ? otherExamples[Math.floor(Math.random() * otherExamples.length)]
                : examples[Math.floor(Math.random() * examples.length)]);
        planFunc.apply(addClass);
    });

    function submitSchedule()
    {
        var schedule = $('#schedule');
        if(schedule.find('.university input').val().trim() == '')
            schedule.find('.university').addClass('error-empty');
        else
            schedule.find('.university').removeClass('error-empty');
        if(schedule.find('.form-actions').is('.invalid'))
        {
            schedule.addClass('invalid-only');
            schedule.find('.class-row.edit.invalid .start-time input,' +
            ' .class-row.edit.invalid .end-time input,' +
            ' .class-row.edit.invalid .start-date input,' +
            ' .class-row.edit.invalid .end-date input').each(function () {
                if($(this).val().trim() == '')
                    $(this).parents('.class-row').addClass('invalid-time');
            });
            return;
        }
        schedule.find('.form-actions').removeClass('valid').addClass('invalid');

        var classes = [];
        schedule.find('.class-row.edit:visible:not(.invalid):not(.blank)').each(function () {
            var row = $(this),
                courseId = (/course-id-([0-9]*)(\s|$)/ig).exec(row.attr('class'))[1],
                dotw = row.find('.day-of-the-week input:checked').map(function (i, x) {return $(x).val();}).get();
            classes[classes.length] = {
                courseId: courseId,
                className: row.find('.class-name input').val(),
                dotw: dotw.join(','),
                start: row.find('.start-time input').val() + ' ' + row.find('.start-date input').val(),
                end: row.find('.end-time input').val() + ' ' + row.find('.end-date input').val(),
                type: row.find('input[name="event-type"]').val()
            };
        });

        $.ajax({
            url: window.callbackPaths['update_schedule'],
            type: 'POST',
            dataType: 'text',
            data: {
                // skip building the schedule if we are in the middle of the buy funnel
                university: schedule.find('.university input').val(),
                classes: classes,
                csrf_token: schedule.find('input[name="csrf_token"]').val()
            },
            success: function (data) {
                schedule.find('.squiggle').stop().remove();
                updateSchedule(data);
            },
            error: function () {
                schedule.find('.squiggle').stop().remove();
            }
        });
    }
    body.on('submit', '#schedule form', function (evt) {
        evt.preventDefault();
        loadingAnimation($(this).find('[value="#save-class"]'));
        setTimeout(submitSchedule, 100);
    });


    var autoFillDate = function () {
        var schedule = $('#schedule');
        var first = $(this).closest('.class-row').add(schedule.find('.class-row')).filter(function () {
                var row = $(this);
                return row.find('.start-time input[type="text"]').val().trim() != '' && row.find('.end-time input[type="text"]').val().trim() != ''
            }).first();
        if(first.find('.start-date input').val() != '' &&
            first.find('.end-date input').val() != '') {
            schedule.find('.class-row').each(function () {
                var row = $(this);
                if (row.find('.class-name input').val() != '' &&
                    row.find('.start-date input').val() == '' &&
                    row.find('.end-date input').val() == '' &&
                    row[0] != first[0]) {
                    // use first rows dates
                    row.find('.start-date input[type="text"]').val(first.find('.start-date input[type="text"]').val());
                    row.find('.end-date input[type="text"]').val(first.find('.end-date input[type="text"]').val());
                    var d = new Date(first.find('.start-date input[type="text"]').val());
                    var d2 = new Date(first.find('.end-date input[type="text"]').val());
                    row.find('.start-date input[type="date"]').val(d.getFullYear() + '-' +
                    (d.getMonth() + 1 < 10 ? ('0' + (d.getMonth() + 1)) : (d.getMonth() + 1)) + '-' +
                    (d.getDate() < 10 ? ('0' + d.getDate()) : d.getDate()));
                    row.find('.end-date input[type="date"]').val(d2.getFullYear() + '-' +
                    (d2.getMonth() + 1 < 10 ? ('0' + (d2.getMonth() + 1)) : (d2.getMonth() + 1)) + '-' +
                    (d2.getDate() < 10 ? ('0' + d2.getDate()) : d2.getDate()));
                    planFunc.apply(row);
                }
            });
        }
    };
    body.on('change', '#schedule .class-name input', autoFillDate);
    body.on('keyup', '#schedule .class-name input', autoFillDate);
    function copyTimes() {
        if(jQuery(this).is('[type="time"]')) {
            jQuery(this).parents('.start-time, .end-time').find('input[type="text"]').timeEntry('setTime', jQuery(this).val());
        }
        if(jQuery(this).is('.is-timeEntry[type="text"]'))
        {
            var t = jQuery(this).timeEntry('getTime');
            if(typeof t != 'undefined' && t != null)
                jQuery(this).parents('.start-time, .end-time').find('input[type="time"]').val((t.getHours() < 10
                    ? ('0' + t.getHours())
                    : t.getHours()) + ':' + (t.getMinutes() < 10
                    ? ('0' + t.getMinutes())
                    : t.getMinutes()) + ':00');
        }
        if(jQuery(this).is('[type="date"]')) {
            var date = new Date;
            date.setFullYear(parseInt($(this).val().substr(0, 4)));
            date.setMonth(parseInt($(this).val().substr(5, 2))-1);
            date.setDate(parseInt($(this).val().substr(8, 2)));
            date.setHours(0);
            date.setMinutes(0);
            date.setSeconds(0);
            date.setMilliseconds(0);
            jQuery(this).parents('.start-date, .end-date').find('input[type="text"]').datepicker('setDate', date);
        }
        if(jQuery(this).is('.hasDatepicker')) {
            var d = $(this).datepicker('getDate');
            if(d != null) {
                jQuery(this).parents('.start-date, .end-date').find('input[type="date"]').val(d.getFullYear() + '-' +
                (d.getMonth() + 1 < 10 ? ('0' + (d.getMonth() + 1)) : (d.getMonth() + 1)) + '-' +
                (d.getDate() < 10 ? ('0' + d.getDate()) : d.getDate()));
            }
        }
        autoFillDate.apply(this);
    }
    body.on('change', '#schedule .start-time input, #schedule .end-time input, ' +
        '#schedule .start-date input, #schedule .end-date input', copyTimes);
    body.on('blur', '#schedule .start-time input, #schedule .end-time input, ' +
        '#schedule .start-date input, #schedule .end-date input', copyTimes);
    body.on('keyup', '#schedule .start-time input, #schedule .end-time input, ' +
        '#schedule .start-date input, #schedule .end-date input', autoFillDate);
    body.on('keyup', '#schedule .class-name input, #schedule .start-time input, #schedule .end-time input, ' +
        '#schedule .start-date input, #schedule .end-date input, #schedule .university input', planFunc);
    body.on('focus', '#schedule .start-time input[type="time"], #schedule .end-time input[type="time"]', function () {
        if($(this).val() == '')
            $(this).val('12:00:00');
    });
    body.on('change', '#schedule .start-date input[type="text"], #schedule .end-date input[type="text"]', function () {
        var row = $(this).parents('.class-row'),
            start = row.find('.start-date input').datepicker('getDate'),
            end = row.find('.end-date input').datepicker('getDate');
        if(start != null) {
            row.find('.end-date input').datepicker('option', 'minDate', start);
        }
        if(end != null) {
            row.find('.start-date input').datepicker('option', 'maxDate', end);
        }
    });
    body.on('change', '#schedule .class-name input, #schedule .day-of-the-week input, #schedule .start-time input, ' +
        '#schedule .end-time input, #schedule .start-date input, #schedule .end-date input, #schedule .university input', planFunc);

    // set default value for university name
    body.on('focus', '#schedule .university input', function () {
        var schedule = $('#schedule');
        schedule.find('[value="#save-class"]').first().css('visibility', 'visible');
    });
    body.on('show', '#schedule', function () {
        var schedule = $('#schedule');
        if(schedule.find('.university input').data('state') == null) {
            schedule.find('.university input').data('state', schedule.find('.university input').val().trim());
            var select = schedule.find('.university input').selectize({
                valueField: 'institution',
                labelField: 'institution',
                searchField: ['institution', 'link', 'state'],
                maxItems: 1,
                create: true,
                options: [schedule.find('.university input').data('data')],
                render: {
                    option: function(item) {
                        return '<div>' +
                        '<span class="title">' +
                        '<span class="name"><i class="icon source"></i>' + item.institution + '</span>' +
                        '<span class="by">' + item.state + '</span>' +
                        '</span>' +
                        '<span class="description">' + item.link + '</span>' +
                        '</div>';
                    }
                },
                load: function(query, callback) {
                    if (query.length < 2) return callback();
                    $.ajax({
                        url: window.callbackPaths['institutions'],
                        dataType:'json',
                        data: {
                            q: query
                        },
                        error: function() {
                            callback();
                        },
                        success: function(res) {
                            callback(res.slice(0, 100));
                        }
                    });
                }
            });
            select.ready(function () {
                select[0].selectize.setValue(schedule.find('.university input').val());
            });
            if(schedule.find('.university input').val().trim() == '')
                select[0].selectize.focus();
        }
        planFunc.apply(schedule.find('.schedule .class-row'));
    });
});