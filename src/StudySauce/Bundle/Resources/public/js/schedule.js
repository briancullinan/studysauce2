$(document).ready(function () {

    var body = $('body'),
        schedule = $('#schedule');

    function planFunc() {
        jQuery(this).each(function () {
            var row = $(this).closest('.class-row');
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

            row.find('.start-date input, .end-date input')
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
                });
            row.find('.start-time input[type="text"], .end-time input[type="text"]')
                .filter(':not(.is-timeEntry)')
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
            if(from != null && to != null && (from.getTime() == to.getTime() || to.getTime() < from.getTime()))
                row.addClass('invalid-time');
            else
                row.removeClass('invalid-time');

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
                    startDate <= endDate2 || endDate >= startDate2)
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
            schedule.find('.university input').val() !=
            schedule.find('.university input').prop('defaultValue'))))
            schedule.removeClass('invalid-only').find('.form-actions').removeClass('invalid').addClass('valid');
        else
            schedule.find('.form-actions').removeClass('valid').addClass('invalid');

        if(schedule.find('.class-row.overlaps:visible').length > 0 ||
            (window.location.pathname != '/schedule2' && schedule.find('.class-row.overlaps').length > 0))
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
    }).ready(function () {
        select[0].selectize.setValue(schedule.find('.university input').val());
    });

    body.on('click', '#schedule a[href="#remove-class"]', function (evt) {
        evt.preventDefault();
        var row = jQuery(this).parents('.class-row');
        $.ajax({
            url: window.callbackPaths['remove_schedule'],
            type: 'POST',
            dataType: 'text',
            data: {
                remove: row.attr('id').substr(0, 4) == 'eid-' ? row.attr('id').substring(4) : null,
                csrf_token: schedule.find('input[name="csrf_token"]').val()
            },
            success: function (data) {
                updateSchedule(data);
            }
        });
    });

    function updateSchedule(data)
    {
        var response = $(data);
        schedule.find('input[name="csrf_token"]').val(response.find('input[name="csrf_token"]').val());
        // update class schedule
        $('.schedule .class-row').remove();
        response.find('.schedule:not(.other) .class-row')
            .appendTo($('.schedule:not(.other)'));

        response.find('.schedule.other .class-row')
            .appendTo($('.schedule.other'));

        planFunc.apply(schedule.find('.schedule .class-row'));
    }

    body.on('click', '#schedule a[href="#add-class"], #schedule a[href="#add-other"]', function (evt) {
        evt.preventDefault();
        var isOther = jQuery(this).is('[href="#add-other"]'),
            examples = ['HIST 101', 'CALC 120', 'MAT 200', 'PHY 110', 'BUS 300', 'ANT 350', 'GEO 400', 'BIO 250', 'CHM 180', 'PHIL 102', 'ENG 100'],
            otherExamples = ['Work', 'Practice', 'Gym', 'Meeting'],
            list = schedule.find('.schedule' + (isOther ? '.other' : ':not(.other)')),
            addClass = list.find('.class-row').first().clone().removeAttr('id')
                .removeClass('read-only').addClass('edit').appendTo(list);
        // reset fields for the new entry
        addClass.find('.class-name input, .start-date input, .end-date input, .start-time input, .end-time input')
            .removeClass('is-timeEntry hasDatepicker').val('');
        addClass.find('.day-of-the-week input').removeAttr('checked').prop('checked', false);
        addClass.find('.class-name input').attr('placeholder', isOther
                ? otherExamples[Math.floor(Math.random() * otherExamples.length)]
                : examples[Math.floor(Math.random() * examples.length)]);
        planFunc.apply(addClass);
    });

    body.on('click', '#schedule a[href="#save-class"]', function (evt) {
        evt.preventDefault();
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
        schedule.find('.class-row.edit.valid:visible, .class-row.valid.edit:visible').each(function () {
            var row = $(this),
                dotw = row.find('.day-of-the-week input:checked').map(function (i, x) {return $(x).val();}).get();
            classes[classes.length] = {
                cid: typeof row.attr('id') != 'undefined' && row.attr('id').substr(0, 4) == 'eid-' ? row.attr('id').substring(4) : null,
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
                updateSchedule(data);
            },
            error: function () {
            }
        });
    });


    var autoFillDate = function () {
        var row = jQuery(this).closest('.class-row'),
            first = schedule.find('.class-row').first();
        if(row.find('.class-name input').val() != '' &&
            row.find('.start-date input').val() == '' &&
            row.find('.end-date input').val() == '' &&
            first.find('.start-date input').val() != '' &&
            first.find('.end-date input').val() != '' &&
            row[0] != schedule.find('.class-row').first()[0])
        {
            // use first rows dates
            row.find('.start-date input').val(first.find('.start-date input').val());
            row.find('.end-date input').val(first.find('.end-date input').val());
        }
    };
    body.on('change', '#schedule .class-name input', autoFillDate);
    body.on('keyup', '#schedule .class-name input', autoFillDate);
    body.on('change', '#schedule .start-time input, #schedule .end-time input, ' +
        '#schedule .start-date input, #schedule .end-date input', function () {
        jQuery(this).parents('.class-row').nextUntil(':not(.class-row)').each(function () {
            autoFillDate.apply(this);
        });
        if(jQuery(this).is('[type="time"]'))
            jQuery(this).parent().find('input[type="text"]').timeEntry('setTime', jQuery(this).val());
        if(jQuery(this).is('.is-timeEntry[type="text"]'))
        {
            var t = jQuery(this).timeEntry('getTime');
            if(typeof t != 'undefined' && t != null)
                jQuery(this).parent().find('input[type="time"]').val((t.getHours() < 10
                    ? ('0' + t.getHours())
                    : t.getHours()) + ':' + (t.getMinutes() < 10
                    ? ('0' + t.getMinutes())
                    : t.getMinutes()) + ':00');
        }
    });
    body.on('keyup', '#schedule .start-time input, #schedule .end-time input, ' +
        '#schedule .start-date input, #schedule .end-date input', function () {
        jQuery(this).parents('.class-row').nextUntil(':not(.class-row)').each(function () {
            autoFillDate.apply(this);
        });
    });
    body.on('keyup', '#schedule .class-name input, #schedule .start-time input, #schedule .end-time input, ' +
        '#schedule .start-date input, #schedule .end-date input, #schedule .university input', function () {
        planFunc.apply(jQuery(this).parents('.class-row'));
    });
    body.on('change', '#schedule .class-name input, #schedule .day-of-the-week input, #schedule .start-time input, ' +
        '#schedule .end-time input, #schedule .start-date input, #schedule .end-date input, #schedule .university input', function () {
        planFunc.apply(jQuery(this).parents('.class-row'));
    });

    // set default value for university name
    if(schedule.find('.university input').val().trim() != '')
        schedule.find('.university input').prop('defaultValue', schedule.find('.university input').val().trim());

    planFunc.apply(schedule.find('.schedule .class-row'));
});