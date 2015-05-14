$(document).ready(function () {

    var body = $('body'),
        date = new Date(),
        original,
        isInitialized = false,
        d = date.getDate(),
        m = date.getMonth(),
        calendar, planTimeout;

    function loadWeek(w, callback, s, e)
    {
        if(planTimeout != null)
            clearTimeout(planTimeout);
        planTimeout = setTimeout(function () {
            var plans = $('#plan');
            $.ajax({
                url: $('.dashboard-home.adviser').length == 1
                    ? window.location.pathname.replace(/adviser\/([0-9]+)(\/.*)?/i, 'adviser/$1/plan/' + w.toJSON() + '/tab')
                    : window.callbackPaths['plan'].replace('/tab', '/' + w.toJSON() + '/tab'),
                type: 'GET',
                dataType: 'text',
                success: function (data) {
                    var tmpEvents = window.planEvents,
                        content = $(data),
                        append = window.planLoaded.length == 1;
                    window.planEvents = [];
                    // merge scripts
                    ssMergeScripts(content.filter('script:not([src])'));
                    for (var i = 0; i < window.planEvents.length; i++) {
                        window.planEvents[i].start = new Date(window.planEvents[i].start);
                        window.planEvents[i].end = new Date(window.planEvents[i].end);
                    }

                    // merge rows
                    if(append && (window.planLoaded[1] == planLoaded[0] + 1 ||
                        (planLoaded[0] == 52 && planLoaded[1] == 1))) {
                        content.find('.head,.session-row').insertAfter(plans.find('.session-row').last());
                        // TODO: resort rows
                    }
                    window.planEvents = $.merge(window.planEvents, tmpEvents);

                    if(callback) {
                        var events = filterEvents(s, e);
                        callback(events);
                    }
                }
            });
        }, 150);
    }

    function filterEvents(s, e) {
        var plans = $('#plan'),
            events = [];
        for (var i = 0; i < window.planEvents.length; i++) {
            if (window.planEvents[i].start.getTime() > s - 86400 && window.planEvents[i].end.getTime() < e + 86400) {
                events[events.length] = window.planEvents[i];
            }
        }
        if (events.length == 0 && $('#plan-intro-1').length == 0) {
            plans.addClass('empty');
            $('#plan-empty').modal({
                backdrop:false,
                keyboard:false,
                show:true
            });
            $(document).off('focusin.bs.modal');
            $('body').removeClass('modal-open');
        }
        else {
            plans.removeClass('empty');
            $('#plan-empty').modal('hide');
        }
        return events;
    }

    function initialize() {
        var plans = $('#plan');
        if (isInitialized)
            return;
        isInitialized = true;

        /* initialize the external events
         -----------------------------------------------------------------*/
        plans.find('.fc-event').each(function() {

            // store data so the calendar knows to render an event upon drop
            $(this).data('event', {
                title: $.trim($(this).text()), // use the element's text as the event title
                stick: true // maintain when user navigates (see docs on the renderEvent method)
            });

            // make the event draggable using jQuery UI
            $(this).draggable({
                zIndex: 999,
                revert: true,      // will cause the event to go back to its
                revertDuration: 0  //  original position after the drag
            });

        });

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
            minTime: '06:00:00',
            maxTime: '26:00:00',
            titleFormat: 'MMMM',
            editable: true,
            draggable: true,
            droppable: true, // this allows things to be dropped onto the calendar
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
                right: 'prev,next today,month,agendaWeek,agendaDay'
            },
            defaultView: 'agendaWeek',
            selectable: false,
            events: function (start, end, timezone, callback) {
                var s = start.unix() * 1000,
                    e = end.unix() * 1000,
                    w = new Date();
                w.setTime(s);
                if (window.planLoaded.indexOf(w.getWeekNumber()) == -1) {
                    loadWeek(w, callback, s, e);
                }
                else {
                    var events = filterEvents(s, e);
                    callback(events);
                }
                if(window.planLoaded.length <= 1) {
                    var w0 = new Date(w.getTime() + 604800000);
                    loadWeek(w0);
                }
            },
            drop: function() {
                // TODO: count down to remove with numbers in event
                // is the "remove after drop" checkbox checked?
                if ($('#drop-remove').is(':checked')) {
                    // if so, remove the element from the "Draggable Events" list
                    $(this).remove();
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

                // find the next event of the same type
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
                    url: window.callbackPaths['plan_update'],
                    type: 'POST',
                    dataType: 'text',
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
                        var content = $(data);
                        window.planEvents = [];
                        window.planLoaded = [];
                        // merge scripts
                        ssMergeScripts(content.filter('script:not([src])'));
                        for (var j = 0; j < window.planEvents.length; j++) {
                            window.planEvents[j].start = new Date(window.planEvents[j].start);
                            window.planEvents[j].end = new Date(window.planEvents[j].end);
                        }

                        // merge rows
                        plans.find('.head,.session-row').remove();
                        content.find('.head,.session-row').insertAfter(plans.find('.sort-by'));

                        if (calendar != null && typeof calendar.fullCalendar != 'undefined')
                            calendar.fullCalendar('refetchEvents');
                    }
                });
            }
        });
    }

    function setupPlan()
    {
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
                show: true
            });
        else
            $('#plan-upgrade').modal('hide');

        if($('#plan-step-0, #plan-step-1, #plan-step-2, #plan-step-3, #plan-step-4, #plan-step-5, #plan-step-6, #plan-step-7').first().modal({
                backdrop: 'static',
                keyboard: false,
                show: true
            }).length == 0) {
            if($('#plan-intro-1').modal({show:true}).length == 0) {
                if (plan.is('.empty-schedule'))
                    $('#plan-empty-schedule').modal({
                        backdrop: 'static',
                        keyboard: false,
                        show: true
                    });
                else
                    $('#plan-empty-schedule').modal('hide');
            }
        }
    }

    body.on('click', '#plan-step-3 a[href="#add-prework"]', function (evt) {
        body.one('click', '#plan a[href="#save-plan"]', function (evt) {
            evt.preventDefault();
            $('#plan-step-32').modal({show: true})
        });
    });

    body.on('click', '#plan-step-32 a[href="#add-spaced-repetition"]', function (evt) {
        body.one('click', '#plan a[href="#save-plan"]', function (evt) {
            evt.preventDefault();
            $('#plan-step-33').modal({show: true})
        });
    });

    body.on('click', '#plan-step-33 a[href="#add-free-study"]', function (evt) {
        body.one('click', '#plan a[href="#save-plan"]', function (evt) {
            evt.preventDefault();
            $('#plan-step-4').modal({show: true})
        });
    });

    // The calendar needs to be in view for sizing information.  This will not initialize when display:none;, so instead
    //   we will activate the calendar only once, when the menu is clicked
    body.on('show', '#plan', function () {
        setupPlan();
    });

    body.on('click', 'a[href="#bill-parents"]', function () {
        $('#plan-upgrade').modal('hide');
    });

    body.on('hidden.bs.modal', '#plan-intro-1', function () {
        $(this).remove();
        var plan = $('#plan');
        if (plan.is(':visible') && plan.is('.empty-schedule'))
            $('#plan-empty-schedule').modal({
                backdrop: 'static',
                keyboard: false,
                show: true
            });
        else
            $('#plan-empty-schedule').modal('hide');
    });

    body.on('hidden.bs.modal', '#bill-parents', function () {
        if (!$('#plan').is(':visible'))
            return;
        $('#plan-upgrade').modal({
            backdrop: 'static',
            keyboard: false,
            show: true
        });
    });

    function submitStep1(evt)
    {
        evt.preventDefault();
        var customization = $('#plan-step-1');
        if(customization.find('.highlighted-link').is('.invalid')) {
            customization.addClass('invalid-only');
            customization.find('.type-required').first().nextAll('label').find('input').first().focus();
            return;
        }
        customization.find('.highlighted-link').removeClass('valid').addClass('invalid');
        loadingAnimation($(this).find('[value="#save-profile"]'));
        var scheduleData = { };
        customization.find('input:checked').each(function () {
            scheduleData[$(this).attr('name')] = $(this).val();
        });

        $.ajax({
            url: window.callbackPaths['profile_update'],
            type: 'POST',
            dataType: 'text',
            data: scheduleData,
            success: function () {
                customization.find('.squiggle').stop().remove();
                // TODO update calendar events
                // TODO: update plan tab
            },
            error: function () {
                customization.find('.squiggle').stop().remove();
            }
        });
    }

    function step1Func() {
        var customization = $('#plan-step-1'),
            valid = true;

        customization.find('input').each(function () {
            var inputSet;
            if((inputSet = customization.find('input[name="' + $(this).attr('name') + '"]')).filter(':checked').length == 0) {
                inputSet.parents('label').prev('h4').addClass('type-required');
                valid = false;
            }
            else
                inputSet.parents('label').prev('h4').removeClass('type-required');
        });

        if(valid)
            customization.removeClass('invalid-only').find('.highlighted-link').removeClass('invalid').addClass('valid');
        else
            customization.find('.highlighted-link').removeClass('valid').addClass('invalid');
    }

    body.on('submit', '#plan-step-1 form', submitStep1);
    body.on('change', '#plan-step-1 input', step1Func);
    body.on('shown.bs.modal', '#plan-step-1', step1Func);

    body.on('scheduled', function () {
        var plan = $('#plan');
        // this page will be reloaded after going through the buy funnel
        if(plan.is('.demo'))
            return;
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
                    if(content.filter('#plan').is('.demo'))
                        plan.addClass('demo');
                    else
                        plan.removeClass('demo');

                    if(content.filter('#plan').is('.empty-schedule')) {
                        plan.addClass('empty-schedule');
                        $('#plan-empty-schedule').modal({
                            backdrop: 'static',
                            keyboard: false,
                            show: plan.is(':visible')
                        });
                    }
                    else {
                        plan.removeClass('empty-schedule');
                        $('#plan-empty-schedule').modal('hide');
                    }

                    // merge rows
                    plan.find('.head,.session-row').remove();
                    content.find('.head,.session-row').insertAfter(plan.find('.sort-by'));

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
            head.nextUntil(':not(.session-row)').each(function () {
                var row = jQuery(this),
                    that = row.find('.class-name');
                if (typeof headings[that.text().trim()] == 'undefined')
                    headings[that.text().trim()] = row;
                else
                    headings[that.text().trim()] = jQuery.merge(headings[that.text().trim()], row);
                that.html('<span class="' + that.find('span').attr('class') + '">&nbsp;</span> ' + head.text().trim());
            });
        });
        var rows = [];
        // order by classes
        if (that.val() == 'class') {
            var keys = [];

            for (var k in headings)
                if (headings.hasOwnProperty(k) && keys.indexOf(k) == -1) {
                    var i = (/checkin([0-9]*)(\s|$)/ig).exec(headings[k].first().attr('class'))[1];
                    if(i != '')
                        keys[i] = k;
                }

            for (var m in headings)
                if (headings.hasOwnProperty(m) && keys.indexOf(m) == -1) {
                    var l = (/checkin([0-9]*)(\s|$)/ig).exec(headings[m].first().attr('class'))[1];
                    if(l == '')
                        keys[keys.length] = m;
                }

            for (var j = 0; j < keys.length; j++) {
                var h1 = headings[keys[j]].filter('.session-row:not(.historic)').length == 0;
                rows = jQuery.merge(rows, jQuery.merge(jQuery('<div class="head ' + (h1 ? 'historic' : '') + '">' + keys[j] + '</div>'), headings[keys[j]].detach()));
            }
        }
        else {
            for (var h in headings) {
                if (!headings.hasOwnProperty(h))
                    continue;
                var h2 = headings[h].filter('.session-row:not(.historic)').length == 0;
                rows = jQuery.merge(rows, jQuery.merge(jQuery('<div class="head ' + (h2 ? 'historic' : '') + '">' + h + '</div>'), headings[h].detach()));
            }
        }
        plans.find('.head').remove();
        $(rows).insertAfter(plans.find('.sort-by'));
    });
});