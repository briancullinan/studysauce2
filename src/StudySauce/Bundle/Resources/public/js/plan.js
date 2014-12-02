$(document).ready(function () {

    var body = $('body'),
        date = new Date(),
        original,
        isInitialized = false,
        d = date.getDate(),
        m = date.getMonth(),
        calendar;

    function initialize() {
        var plans = $('#plan');
        if (isInitialized)
            return;
        isInitialized = true;
        // find min an max time
        var early = -1,
            morning = 10,
            late = 18,
            localOffset = (3600000 * 7) - (new Date()).getTimezoneOffset() * 60000;
        for (var i = 0; i < window.planEvents.length; i++) {
            var s = new Date(new Date(window.planEvents[i].start) - localOffset),
                e = new Date(new Date(window.planEvents[i].end) - localOffset);
            window.planEvents[i].start = s;
            window.planEvents[i].end = e;

            if (window.planEvents[i].allDay ||
                window.planEvents[i].className.indexOf('event-type-z') > -1 ||
                window.planEvents[i].className.indexOf('event-type-m') > -1)
                continue;
            if (e.getHours() < 5 && e.getHours() > early)
                early = e.getHours();
            if (e.getHours() > late)
                late = e.getHours();
            if (s.getHours() > 3 && s.getHours() < morning)
                morning = s.getHours();
        }

        // use early morning as end time
        var min, max;
        if (early > -1)
            max = (24 + early + 1) + ':00:00';
        else
            max = (late + 1) + ':00:00';
        min = morning + ':00:00';

        calendar = $('#calendar').fullCalendar({
            minTime: min,
            maxTime: max,
            titleFormat: 'MMMM',
            editable: true,
            draggable: true,
            aspectRatio: 1.9,
            height: 'auto',
            timezone: 'local',
            timeslotsPerHour: 4,
            slotEventOverlap: false,
            slotMinutes: 15,
            firstHour: new Date().getHours(),
            eventRender: function (event, element) {
                element.find('.fc-title').html(event.title);
                return true;
            },
            header: {
                left: '',
                center: '',
                right: 'prev,next today'
            },
            defaultView: 'agendaWeek',
            selectable: false,
            events: function (start, end, timezone, callback) {
                var events = [],
                    s = start.unix() * 1000,
                    e = end.unix() * 1000,
                    w = new Date(),
                    filterEvents = function () {
                        for (var i = 0; i < window.planEvents.length; i++) {
                            if (window.planEvents[i].start.getTime() > s - 86400 && window.planEvents[i].end.getTime() < e + 86400) {
                                events[events.length] = window.planEvents[i];
                            }
                        }
                        if (events.length == 0) {
                            plans.addClass('empty');
                            $('#plan-empty').modal({backdrop: false});
                        }
                        else {
                            plans.removeClass('empty');
                            $('#plan-empty').modal('hide');
                        }
                    };
                w.setTime(s);
                if (window.planLoaded.indexOf(w.getWeekNumber()) == -1) {
                    $.ajax({
                        url: window.callbackPaths['plan'].replace('/tab', '/' + w.toJSON() + '/tab'),
                        type: 'GET',
                        dataType: 'text',
                        success: function (data) {
                            var tmpEvents = window.planEvents,
                                content = $(data);
                            window.planEvents = [];
                            // merge scripts
                            ssMergeScripts(content.filter('script:not([src])'));
                            for (var i = 0; i < window.planEvents.length; i++) {
                                window.planEvents[i].start = new Date(window.planEvents[i].start);
                                window.planEvents[i].end = new Date(window.planEvents[i].end);
                            }

                            if (w.getWeekNumber() == window.planLoaded[0] + 1)
                                content.find('.head,.session-row').insertAfter(plans.find('.session-row').last());
                            // TODO: do this after merge if events aren't showing up
                            filterEvents();
                            // TODO: merge rows
                            window.planEvents = $.merge(window.planEvents, tmpEvents);
                            callback(events);
                        }
                    });
                }
                else {
                    filterEvents();
                    callback(events);
                }
            },
            eventClick: function (calEvent) {
                // change the border color just for fun
                if (plans.find('.event-id-' + calEvent.eventId).length > 0) {
                    if (!plans.find('.event-id-' + calEvent.eventId).is('.selected'))
                        plans.find('.event-id-' + calEvent.eventId).find('.assignment').trigger('click');
                    plans.find('.event-id-' + calEvent.eventId).scrollintoview(DASHBOARD_MARGINS);
                }

            },
            eventDragStart: function (event) {
                original = new Date(event.start.unix() * 1000);
            },
            eventDragStop: function (event, jsEvent, ui, view) {
            },
            eventDrop: function (event, delta, revertFunc) {

                if (event.allDay) {
                    revertFunc();
                    return;
                }

                var prev = null, next = null;

                for (var i = 0; i < window.planEvents.length; i++) {
                    if ((window.planEvents[i].className[1] == event.className[1] ||
                        window.planEvents[i].className[0] == event.className[1]) &&
                        window.planEvents[i].className[0] != 'event-type-r' &&
                        window.planEvents[i].className[0] != 'event-type-h' &&
                        window.planEvents[i].className[0] != 'event-type-d' &&
                        i != event.eventId) {
                        // TODO: update this if classes are draggable
                        if ((next == null || window.planEvents[i].start.getTime() < next.start.getTime()) &&
                            window.planEvents[i].start.getTime() > original.getTime()) {
                            next = window.planEvents[i];
                        }
                        else if ((prev == null || window.planEvents[i].start.getTime() > prev.start.getTime()) &&
                            window.planEvents[i].start.getTime() < original.getTime())
                            prev = window.planEvents[i];
                    }
                }

                // check for last event of type or first event of type
                if ((prev != null && event.start.getTime() < prev.end.getTime()) ||
                    (next != null && event.end.getTime() > next.start.getTime())) {
                    revertFunc();
                    return;
                }

                // TODO: fix this
                $.ajax({
                    url: '/node/move/schedule',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        eventId: event['eventId'],
                        start: event['start'].toJSON(),
                        end: event['end'].toJSON(),
                        type: event['className'].indexOf('event-type-p') != -1
                            ? 'p'
                            : (event['className'].indexOf('event-type-sr') != -1
                            ? 'sr'
                            : (event['className'].indexOf('event-type-f') != -1
                            ? 'f'
                            : (event['className'].indexOf('event-type-o') != -1
                            ? 'o'
                            : (event['className'].indexOf('event-type-d') != -1
                            ? 'd'
                            : ''))))
                    },
                    error: revertFunc,
                    success: function (data) {
                        // update calendar events
                        window.planEvents = data.events;
                        window.planLoaded = [event['start'].getWeekNumber()];
                        for (var i = 0; i < window.planEvents.length; i++) {
                            window.planEvents[i].start = new Date(window.planEvents[i].start);
                            window.planEvents[i].end = new Date(window.planEvents[i].end);
                        }
                        if (calendar != null && typeof calendar.fullCalendar != 'undefined')
                            calendar.fullCalendar('refetchEvents');
                    }
                });
            }
        });
    }

    // The calendar needs to be in view for sizing information.  This will not initialize when display:none;, so instead
    //   we will activate the calendar only once, when the menu is clicked
    body.on('show', '#plan', function () {
        var plan = $('#plan'),
            callback = function () {
                if ($('#calendar:visible').length > 0) {
                    initialize();
                    $('#calendar').fullCalendar('refetchEvents');
                }
                else
                    setTimeout(callback, 150);
                // show unpaid dialog
            };
        setTimeout(callback, 150);
        if (plan.is('.demo'))
            $('#plan-upgrade').modal({
                backdrop: 'static',
                keyboard: false,
                modalOverflow: true
            });
        else
            $('#plan-upgrade').modal('hide');
        $('#plan-intro-1').modal();
    });

    body.on('click', 'a[href="#bill-parents"]', function () {
        $('#plan-upgrade').modal('hide');
    });

    body.on('hidden.bs.modal', '#bill-parents', function () {
        if (!$('#plan').is(':visible'))
            return;
        $('#plan-upgrade').modal({
            backdrop: 'static',
            keyboard: false,
            modalOverflow: true
        });
    });

    body.on('scheduled', function () {
        var plan = $('#plan');
        // update classes
        setTimeout(function () {
            $.ajax({
                url: window.callbackPaths['plan'],
                type: 'GET',
                dataType: 'text',
                success: function (data) {
                    var content = $(data);
                    window.planEvents = [];
                    window.planLoaded = [];
                    ssMergeScripts(content.filter('script:not([src])'));
                    if(content.find('#metrics').is('.demo'))
                        plan.addClass('demo');
                    else
                        plan.removeClass('demo');

                    // reset calendar
                    $('#calendar').fullCalendar('destroy');
                    isInitialized = false;
                    plan.filter(':visible').trigger('show');
                }
            });
        }, 100);
    });

    body.on('click', '#plan .sort-by a[href="#expand"]', function () {
        var plans = $('#plan');
        if (plans.is('.fullcalendar')) {
            plans.removeClass('fullcalendar');
            $('#calendar').fullCalendar('option', 'height', 500);
        }
        else {
            plans.addClass('fullcalendar');
            $('#calendar').fullCalendar('option', 'height', 'auto');
        }
    });

    body.on('change', '#plan .completed input, .plan-widget .completed input', function () {
        var that = jQuery(this),
            row = that.parents('.session-row'),
            eventId = (/event-id-([0-9]+)(\s|$)/ig).exec(row.attr('class'))[1];

        $.ajax({
            url: window.callbackPaths['plan_complete'],
            type: 'POST',
            dataType: 'json',
            data: {
                eventId: eventId,
                completed: that.is(':checked')
            },
            success: function () {
                if (that.is(':checked'))
                    $('.session-row.event-id-' + eventId).addClass('done');
                else
                    $('.session-row.event-id-' + eventId).removeClass('done');
            }
        });
    });

    body.on('change', '#plan .sort-by .checkbox input', function () {
        var plan = $('#plan');
        if(jQuery(this).is(':checked'))
            plan.addClass('show-historic');
        else
            plan.removeClass('show-historic');
    });

    body.on('change', '#plan .sort-by input[type="radio"]', function () {
        var plans = $('#plan');
        var headings = {},
            that = jQuery(this);
        plans.find('.head').each(function () {
            var head = jQuery(this);
            head.nextUntil('.head,p:last-of-type').each(function () {
                var row = jQuery(this),
                    that = row.find('.class-name');
                if (typeof headings[that.text().trim()] == 'undefined')
                    headings[that.text().trim()] = row;
                else
                    headings[that.text().trim()] = jQuery.merge(headings[that.text().trim()], row);
                that.text(head.text().trim());
            });
        });
        var rows = [];
        if (that.val() == 'class') {
            var keys = [];

            for (var i = 0; i < window.classNames.length; i++)
                if (typeof headings[window.classNames[i]] != 'undefined')
                    keys[keys.length] = window.classNames[i];

            for (var k in headings)
                if (headings.hasOwnProperty(k) && keys.indexOf(k) == -1)
                    keys[keys.length] = k;

            for (var j = 0; j < keys.length; j++) {
                var h1 = headings[keys[j]].filter('.row:not(.historic)').length == 0;
                rows = jQuery.merge(rows, jQuery.merge(jQuery('<div class="head ' + (h1 ? 'historic' : '') + '">' + keys[j] + '</div>'), headings[keys[j]].detach()));
            }
        }
        else {
            for (var h in headings) {
                if (!headings.hasOwnProperty(h))
                    continue;
                var h2 = headings[h].filter('.row:not(.historic)').length == 0;
                rows = jQuery.merge(rows, jQuery.merge(jQuery('<div class="head ' + (h2 ? 'historic' : '') + '">' + h + '</div>'), headings[h].detach()));
            }
        }
        plans.find('.head, .row').replaceWith(rows);
    });
});