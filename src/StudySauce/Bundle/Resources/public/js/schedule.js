$(document).ready(function () {

    var body = $('body'),
        deleteCount = 0;

    function planFunc() {
        var schedule = $('#schedule');
        schedule.find('.term-row').each(function () {
            var term = $(this);
            term.find('.class-row').each(function () {
                var row = $(this);
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
                        var that = $(this),
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
                var from = row.find('.start-time input.is-timeEntry').timeEntry('getTime'),
                    to = row.find('.end-time input.is-timeEntry').timeEntry('getTime');
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
                var startDate = new Date(row.find('.start-date input.hasDatepicker').val());
                var endDate = new Date(row.find('.end-time input.hasDatepicker').val());
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
                term.find('.class-row').not(row).each(function () {
                    var that = $(this);
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
            });
        });

        var term = schedule.find('.term-row:visible').first();
        if(term.find('.class-row.edit.invalid:visible').length == 0 &&
            term.find('.class-row.overlaps:visible').length == 0 &&
            term.find('.class-row.invalid-time:not(.blank):visible').length == 0 &&
            term.find('.class-row.valid:not(.blank)').length > 0 &&
            term.find('.university input').val().trim() != '' && (
                // make sure there are rows to save that are not blank
                term.find('.class-row.edit.valid:visible:not(.blank)').length > 0 ||
                // rows can be read-only and still need to save if university name changes
                term.find('.university input').val().trim() != term.find('.university input').data('state') ||
                // we may need to save deleted rows
                term.find('.class-row.deleted').length > 0))
            term.removeClass('invalid-only').find('.form-actions').removeClass('invalid').addClass('valid');
        else
            term.find('.form-actions').removeClass('valid').addClass('invalid');

        if(term.find('.class-row.overlaps:visible').length > 1)
            term.addClass('overlaps');
        else
            term.removeClass('overlaps');

        if(term.find('.class-row.invalid-time:visible').length > 0)
            term.addClass('invalid-time');
        else
            term.removeClass('invalid-time');
    }

    body.on('click', '#schedule a[href="#edit-class"]', function (evt) {
        evt.preventDefault();
        var that = $(this),
            row = that.parents('.class-row').removeClass('read-only').addClass('edit');
        planFunc();
        row.find('.start-time input[type="text"], .end-time input[type="text"]').trigger('change');
    });

    body.on('click', '#schedule a[href="#remove-class"]', function (evt) {
        evt.preventDefault();
        deleteCount++;
        var row = $(this).parents('.class-row'),
            schedule = $('#schedule'),
            container = row.parents('.schedule');
        if(deleteCount == 4) {
            $('#new-schedule').modal({show:true});
        }
        // clear and hide the class row
        row.find('.class-name input, .start-date input, .end-date input').val('');
        row.find('.day-of-the-week input').prop('checked', false);
        row.removeClass('invalid').addClass('blank deleted').hide();
        schedule.find('[value="#save-class"]').css('visibility', 'visible');
        // reset color bubbles
        if(container.is(':not(.other)')) {
            container.find('.class-row:visible').each(function (i) {
                $(this).find('.class-name i').attr('class', 'class' + i);
            });
        }
        if(container.find('.class-row:visible').length == 0)
        {
            if(container.is('.other'))
                container.find('a[href="#add-class"]').trigger('click');
            else
                container.find('a[href="#add-class"]').trigger('click').trigger('click').trigger('click').trigger('click').trigger('click').trigger('click');
        }
    });

    function updateSchedule(data)
    {
        // update all terms because there is a check for unsaved changes
        var schedule = $('#schedule'),
            response = $(data);
        schedule.find('input[name="csrf_token"]').val(response.find('input[name="csrf_token"]').val());
        // remove blank terms
        schedule.find('.term-row').each(function () {
            if($(this).find('.class-row:not(.blank)').length == 0)
                $(this).remove();
        });

        // update remainging terms
        response.find('.term-row').each(function (j) {
            var responseTerm = $(this),
                responseId = (/schedule-id-([0-9]*)(\s|$)/ig).exec(responseTerm.attr('class'))[1],
                term = schedule.find('.term-row').eq(j);
            // update schedule id if newly added
            term.removeClass('.schedule-id-').addClass('schedule-id-' + responseId);

            term.find('.university input').data('state', term.find('.university input').val().trim());
            term.find('.schedule .class-row.valid').remove();
            responseTerm.find('.schedule:not(.other) .class-row')
                .prependTo(term.find('.schedule:not(.other)'));

            responseTerm.find('.schedule.other .class-row')
                .prependTo(term.find('.schedule.other'));
        });
        if(schedule.find('.term-row').length == 1)
            schedule.removeClass('multi').addClass('single');
        schedule.scrollintoview(DASHBOARD_MARGINS);
        schedule.find('[value="#save-class"]').css('visibility', 'hidden');
        planFunc();
        body.trigger('scheduled');
    }

    function addClass()
    {
        var list = $(this).parents('.schedule'),
            examples = ['HIST 101', 'CALC 120', 'MAT 200', 'PHY 110', 'BUS 300', 'ANT 350', 'GEO 400', 'BIO 250', 'CHM 180', 'PHIL 102', 'ENG 100'],
            otherExamples = ['Work', 'Practice', 'Gym', 'Meeting'],
            addClass = list.find('.class-row').first().clone().removeAttr('id')
                .removeClass('read-only deleted').addClass('edit').show().insertBefore(list.find('.form-actions'));
        // reset fields for the new entry
        addClass.attr('class', addClass.attr('class').replace(/course-id-([0-9]*)(\s|$)/ig, ' course-id- '));
        addClass.find('.class-name input, .start-date input, .end-date input, .start-time input, .end-time input')
            .removeClass('is-timeEntry hasDatepicker').removeAttr('id').val('');
        addClass.find('.day-of-the-week input').removeAttr('checked').prop('checked', false);
        addClass.find('.class-name input').attr('placeholder', list.is('.other')
            ? otherExamples[Math.floor(Math.random() * otherExamples.length)]
            : examples[Math.floor(Math.random() * examples.length)]);
    }

    body.on('click', '#schedule a[href="#add-class"]', function (evt) {
        evt.preventDefault();
        addClass.apply(this);
        planFunc();
    });

    body.on('click', '#schedule a[href="#prev-schedule"]', function (evt) {
        evt.preventDefault();
        if($(this).is('.disabled'))
            return;
        var schedule = $('#schedule');
        schedule.find('.term-row:has(+ .term-row:visible)').show().next().hide();
        schedule.find('a[href="#next-schedule"]').removeClass('disabled');
        if(schedule.find('.term-row:visible').is(schedule.find('.term-row').first()))
            $(this).addClass('disabled');
        else
            $(this).removeClass('disabled');
        schedule.find('.schedule-history .term-label').html(schedule.find('.term-row:visible input[name="term-label"]').val());
    });

    body.on('change', '#manage-terms select', function () {
        // reorder terms
        var dialog = $('#manage-terms');
        dialog.find('.term-row').sort(function (a, b) {
            var timeA = $(a).find('select').val().split('/'),
                timeB = $(b).find('select').val().split('/');
            if(parseInt(timeA[1]) * 12 + parseInt(timeA[0]) > parseInt(timeB[1]) * 12 + parseInt(timeB[0])) {
                return -1;
            }
            if(parseInt(timeA[1]) * 12 + parseInt(timeA[0]) < parseInt(timeB[1]) * 12 + parseInt(timeB[0])) {
                return 1;
            }
            return 0;
        }).detach().insertBefore(dialog.find('a[href="#add-term"]'));
        dialog.find('.term-row').each(function (i) {
            $(this).find('.term-count').html((i + 1) + '.');
        });
    });

    function addTerm()
    {
        var dialog = $('#manage-terms'),
            newTerm = dialog.find('.term-row').first().clone().insertAfter(dialog.find('.term-row').last());
        newTerm.removeClass('read-only');
        newTerm.find('.term-count').html(dialog.find('.term-row:not(.deleted)').length + '.');
        newTerm.find('option:first-child').html('Select term');
        newTerm.find('select').val('');
        newTerm.attr('class', newTerm.attr('class').replace(/schedule-id-([0-9]*)(\s|$)/ig, ' schedule-id- '));
    }

    body.on('click', '#manage-terms a[href="#add-term"]', function (evt) {
        evt.preventDefault();
        addTerm();
    });

    body.on('click', '#manage-terms a[href="#remove-term"]', function (evt) {
        evt.preventDefault();
        var dialog = $('#manage-terms'),
            row = $(this).parents('.term-row');
        row.addClass('deleted');
        if(row.is('.schedule-id-'))
            row.remove();
        else
            row.hide();
        dialog.find('.term-row').each(function (i) {
            $(this).find('.term-count').html((i + 1) + '.');
        });
    });

    body.on('click', '#schedule a[href="#next-schedule"]', function (evt) {
        evt.preventDefault();
        if($(this).is('.disabled'))
            return;
        var schedule = $('#schedule');
        schedule.find('.term-row:visible').hide().next('.term-row').show();
        schedule.find('a[href="#prev-schedule"]').removeClass('disabled');
        if(schedule.find('.term-row:visible').is(schedule.find('.term-row').last()))
            $(this).addClass('disabled');
        else
            $(this).removeClass('disabled');
        schedule.find('.schedule-history .term-label').html(schedule.find('.term-row:visible input[name="term-label"]').val());
    });

    function submitSchedule(evt)
    {
        evt.preventDefault();
        var schedule = $('#schedule'),
            terms = [];

        // cancel save if the visible term has an invalid entry
        if(schedule.find('.form-actions:visible').is('.invalid'))
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
        loadingAnimation(schedule.find('[value="#save-class"]'));

        schedule.find('.term-row').each(function () {
            var term = $(this);
            if(term.find('.university input').val().trim() == '')
                term.find('.university').addClass('error-empty');
            else
                term.find('.university').removeClass('error-empty');

            var classes = [],
                scheduleId = (/schedule-id-([0-9]*)(\s|$)/ig).exec(term.attr('class'))[1];
            term.find('.class-row.edit:not(.invalid):not(.blank), .class-row.deleted').each(function () {
                var row = $(this),
                    courseId = (/course-id-([0-9]*)(\s|$)/ig).exec(row.attr('class'))[1],
                    dotw = row.find('.day-of-the-week input:checked').map(function (i, x) {return $(x).val();}).get();
                // remove blank rows
                if(row.is('.deleted')) {
                    classes[classes.length] = {
                        courseId: courseId,
                        remove: true
                    };
                }
                else {
                    classes[classes.length] = {
                        courseId: courseId,
                        className: row.find('.class-name input').val(),
                        dotw: dotw.join(','),
                        start: row.find('.start-time input').val() + ' ' + row.find('.start-date input').val(),
                        end: row.find('.end-time input').val() + ' ' + row.find('.end-date input').val(),
                        type: row.find('input[name="event-type"]').val(),
                        remove: false
                    };
                }
            });

            var termData = {
                university : term.find('.university input').val(),
                scheduleId : scheduleId,
                classes : classes
            };
            termData['remove'] = term.find('.class-row:not(.blank)').length == 0;
            terms[terms.length] = termData;
        });

        $.ajax({
            url: window.callbackPaths['update_schedule'],
            type: 'POST',
            dataType: 'text',
            data: {
                // skip building the schedule if we are in the middle of the buy funnel
                terms: terms,
                csrf_token: schedule.find('input[name="csrf_token"]').val()
            },
            success: function (data) {
                schedule.find('.squiggle').stop().remove();
                updateSchedule(data);
            },
            error: function () {
                term.find('.squiggle').stop().remove();
            }
        });
    }
    body.on('submit', '#schedule form', submitSchedule);

    function createSchedule()
    {
        var schedule = $('#schedule'),
            newTerm = schedule.find('.term-row').first().clone().insertBefore(schedule.find('.term-row').first()),
            oldRows = newTerm.find('.class-row');
        schedule.removeClass('single').addClass('multi');
        newTerm.attr('class', newTerm.attr('class').replace(/schedule-id-([0-9]*)(\s|$)/ig, ' schedule-id- '));
        for(var i = 0; i < 6; i++)
        {
            addClass.apply(newTerm.find('.schedule:not(.other) a[href="#add-class"]'));
        }
        addClass.apply(newTerm.find('.schedule.other a[href="#add-class"]'));
        oldRows.remove();
        schedule.find('.term-row').hide();
        newTerm.show();
        if(schedule.find('.term-row:visible').is(schedule.find('.term-row').last()))
            schedule.find('a[href="#next-schedule"]').addClass('disabled');
        else
            schedule.find('a[href="#next-schedule"]').removeClass('disabled');
        if(schedule.find('.term-row:visible').is(schedule.find('.term-row').first()))
            schedule.find('a[href="#prev-schedule"]').addClass('disabled');
        else
            schedule.find('a[href="#prev-schedule"]').removeClass('disabled');
        newTerm.find('input[name="term-label"]').val('New term');
        planFunc();
    }

    body.on('click', '#schedule a[href*="#create-schedule"]', function (evt) {
        evt.preventDefault();
        var schedule = $('#schedule');
        createSchedule.apply(this);
        // add new term to term manager
        addTerm();
        var dialog = $('#manage-terms');
        dialog.find('.schedule-id-').detach().insertBefore(dialog.find('.term-row').first());
        dialog.find('.term-row').each(function (i) {
            $(this).find('.term-count').html((i + 1) + '.');
        });
        schedule.find('.schedule-history .term-label').html(schedule.find('.term-row input[name="term-label"]').val());
    });

    function autoFillDate() {
        var schedule = $(this).parents('.term-row');
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
                    planFunc();
                }
            });
        }
    }

    body.on('change', '#schedule .class-name input', autoFillDate);
    body.on('keyup', '#schedule .class-name input', autoFillDate);

    function copyTimes() {
        if($(this).is('[type="time"]')) {
            $(this).parents('.start-time, .end-time').find('input[type="text"]').timeEntry('setTime', $(this).val());
        }
        if($(this).is('.is-timeEntry[type="text"]'))
        {
            var t = $(this).timeEntry('getTime');
            if(typeof t != 'undefined' && t != null)
                $(this).parents('.start-time, .end-time').find('input[type="time"]').val((t.getHours() < 10
                    ? ('0' + t.getHours())
                    : t.getHours()) + ':' + (t.getMinutes() < 10
                    ? ('0' + t.getMinutes())
                    : t.getMinutes()) + ':00');
        }
        if($(this).is('[type="date"]')) {
            var date = new Date;
            date.setFullYear(parseInt($(this).val().substr(0, 4)));
            date.setMonth(parseInt($(this).val().substr(5, 2))-1);
            date.setDate(parseInt($(this).val().substr(8, 2)));
            date.setHours(0);
            date.setMinutes(0);
            date.setSeconds(0);
            date.setMilliseconds(0);
            $(this).parents('.start-date, .end-date').find('input[type="text"]').datepicker('setDate', date);
        }
        if($(this).is('.hasDatepicker')) {
            var d = $(this).datepicker('getDate');
            if(d != null) {
                $(this).parents('.start-date, .end-date').find('input[type="date"]').val(d.getFullYear() + '-' +
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
        schedule.find('[value="#save-class"]').css('visibility', 'visible');
    });

    body.on('show', '#schedule', function () {
        var schedule = $('#schedule');
        schedule.find('.term-row').each(function () {
            var term = $(this);
            if(term.find('.university input').data('state') == null) {
                term.find('.university input').data('state', term.find('.university input').val().trim());
                var select = term.find('.university input').selectize({
                    valueField: 'institution',
                    labelField: 'institution',
                    searchField: ['institution', 'link', 'state'],
                    maxItems: 1,
                    create: true,
                    options: [term.find('.university input').data('data')],
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
                    select[0].selectize.setValue(term.find('.university input').data('state'));
                });
                if(term.find('.university input').val().trim() == '')
                    select[0].selectize.focus();
            }
        });
        if(schedule.is('.needs-new-schedule')) {
            $('#new-schedule').modal({show:true});
        }
    });

    body.on('hidden.bs.modal', '#new-schedule', function () {
        $(this).remove();
    });


});