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
        if (!isInitialized)
        {
            // find min an max time
            var early = -1,
                morning = 10,
                late = 18,
                localOffset = 0; //(3600000 * 7) - (new Date()).getTimezoneOffset() * 60000;
            for(var i = 0; i < window.planEvents.length; i++)
            {
                var s = new Date(new Date(window.planEvents[i].start) - localOffset),
                    e = new Date(new Date(window.planEvents[i].end) - localOffset);
                window.planEvents[i].start = s;
                window.planEvents[i].end = e;

                if(window.planEvents[i].allDay ||
                    window.planEvents[i].className.indexOf('event-type-z') > -1 ||
                    window.planEvents[i].className.indexOf('event-type-m') > -1)
                    continue;
                if(e.getHours() < 5 && e.getHours() > early)
                    early = e.getHours();
                if(e.getHours() > late)
                    late = e.getHours();
                if(s.getHours() > 3 && s.getHours() < morning)
                    morning = s.getHours();
            }

            // use early morning as end time
            var min,max;
            if(early > -1)
                max = (24 + early + 1) + ':00:00';
            else
                max = (late + 1) + ':00:00';
            min = morning + ':00:00';

            calendar = $('#calendar').fullCalendar(
                {
                    minTime: min,
                    maxTime: max,
                    titleFormat: 'MMMM',
                    editable: true,
                    draggable: true,
                    aspectRatio: 1.9,
                    height:'auto',
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
                                    plans.find('#empty-week').dialog();
                                }
                                else {
                                    plans.removeClass('empty');
                                    plans.find('#empty-week').dialog('hide');
                                }
                            };
                        w.setTime(s);
                        if(window.planLoaded.indexOf(w.getWeekNumber()) == -1) {
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
                                    for(var i = 0; i < window.planEvents.length; i++)
                                    {
                                        window.planEvents[i].start = new Date(window.planEvents[i].start);
                                        window.planEvents[i].end = new Date(window.planEvents[i].end);
                                    }

                                    if(w.getWeekNumber() == window.planLoaded[0] + 1)
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
                    eventClick: function(calEvent) {
                        // var eid =  calEvent._id.substring(3);
                        // change the border color just for fun
                        if(plans.find('#eid-' + calEvent.cid).length > 0)
                        {
                            if(!plans.find('#eid-' + calEvent.cid).is('.selected'))
                                plans.find('#eid-' + calEvent.cid).find('.field-name-field-assignment').trigger('click');
                            plans.find('#eid-' + calEvent.cid).scrollintoview({padding: {top:120,bottom:100,left:0,right:0}});
                        }

                    },
                    eventDragStart: function (event) {
                        original = new Date(event.start.unix() * 1000);
                    },
                    eventDragStop: function (event, jsEvent, ui, view) {
                    },
                    eventDrop: function (event, delta, revertFunc) {

                        if(event.allDay)
                        {
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
                                i != event.cid)
                            {
                                // TODO: update this if classes are draggable
                                if ((next == null || window.planEvents[i].start.getTime() < next.start.getTime()) &&
                                    window.planEvents[i].start.getTime() > original.getTime())
                                {
                                    next = window.planEvents[i];
                                }
                                else if((prev == null || window.planEvents[i].start.getTime() > prev.start.getTime()) &&
                                    window.planEvents[i].start.getTime() < original.getTime())
                                    prev = window.planEvents[i];
                            }
                        }

                        // check for last event of type or first event of type
                        if ((prev != null && event.start.getTime() < prev.end.getTime()) ||
                            (next != null && event.end.getTime() > next.start.getTime()))
                        {
                            revertFunc();
                            return;
                        }


                        $.ajax({
                            url: '/node/move/schedule',
                            type: 'POST',
                            dataType: 'json',
                            data: {
                                cid: event['cid'],
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
                                for(var i = 0; i < window.planEvents.length; i++)
                                {
                                    window.planEvents[i].start = new Date(window.planEvents[i].start);
                                    window.planEvents[i].end = new Date(window.planEvents[i].end);
                                }
                                if(calendar != null && typeof calendar.fullCalendar != 'undefined')
                                    calendar.fullCalendar('refetchEvents');
                            }
                        });
                    }
                });

            isInitialized = true;
        }
    }

    // The calendar needs to be in view for sizing information.  This will not initialize when display:none;, so instead
    //   we will activate the calendar only once, when the menu is clicked, this assumes #hash detection works, and
    //   it triggers the menu clicking
    body.on('show', '#plan', function () {
        initialize();
        if($('#calendar:visible').length > 0)
            $('#calendar').fullCalendar('refetchEvents');
    });
    body.find('#plan:visible').trigger('show');

    body.on('click', '#plan .sort-by a[href="#expand"]', function () {
        var plans = $('#plan');
        if(plans.is('.fullcalendar'))
        {
            plans.removeClass('fullcalendar');
            $('#calendar').fullCalendar('option', 'height', 500);
        }
        else
        {
            plans.addClass('fullcalendar');
            $('#calendar').fullCalendar('option', 'height', 'auto');
        }
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
                if(typeof headings[that.text().trim()] == 'undefined')
                    headings[that.text().trim()] = row;
                else
                    headings[that.text().trim()] = jQuery.merge(headings[that.text().trim()], row);
                that.text(head.text().trim());
            });
        });
        var rows = [];
        if(that.val() == 'class')
        {
            var keys = [];

            for(var i = 0; i < window.classNames.length; i++)
                if(typeof headings[window.classNames[i]] != 'undefined')
                    keys[keys.length] = window.classNames[i];

            for(var k in headings)
                if(headings.hasOwnProperty(k) && keys.indexOf(k) == -1)
                    keys[keys.length] = k;

            for(var j = 0; j < keys.length; j++)
            {
                var h1 = headings[keys[j]].filter('.row:not(.hide)').length == 0;
                rows = jQuery.merge(rows, jQuery.merge(jQuery('<div class="head ' + (h1 ? 'hide' : '') + '">' + keys[j] + '</div>'), headings[keys[j]].detach()));
            }
        }
        else
        {
            for(var h in headings)
            {
                if(!headings.hasOwnProperty(h))
                    continue;
                var h2 = headings[h].filter('.row:not(.hide)').length == 0;
                rows = jQuery.merge(rows, jQuery.merge(jQuery('<div class="head ' + (h2 ? 'hide' : '') + '">' + h + '</div>'), headings[h].detach()));
            }
        }
        plans.find('.head, .row').replaceWith(rows);
    });
});