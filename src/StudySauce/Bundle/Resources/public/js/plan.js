$(document).ready(function () {

    var body = $('body'),
        original,
        isInitialized = false,
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

    body.on('dblclick', '#plan .fc-event', function () {
        $('#edit-event').modal({show:true});
    });

    body.on('click', '#plan .fc-agendaDay-button', function () {
        $('#plan').addClass('session-selected');
    });

    body.on('click', '#plan .fc-agendaWeek-button, #plan .fc-month-button', function () {
        $('#plan').removeClass('session-selected');
    });

    var prevDropLocation, shortlist;
    function shouldRevert() {

        if($(this).is('.event-type-p')) {
            var next = null;
            // find the next occurring event of the same class, must come before that.
            for(var i = 0; i < shortlist.length; i++) {
                if(shortlist[i].start.getTime() > prevDropLocation.start.valueOf() &&
                    (next == null || shortlist[i].start.getTime() < next.start.getTime())) {
                    next = shortlist[i];
                }
            }

            if(next == null || prevDropLocation.start.valueOf() + 3600000 > next.start.getTime() ||
                prevDropLocation.start.valueOf() < next.start.getTime() - 86400000) {
                $(this).addClass('invalid');
                return true;
            }
        }
        else if($(this).is('.event-type-sr'))
        {
            var prev = null;
            // find the prev occurring event of the same class, must come before that.
            for(var i = 0; i < shortlist.length; i++) {
                if(shortlist[i].start.getTime() < prevDropLocation.start.valueOf() &&
                    (prev == null || shortlist[i].start.getTime() > prev.start.getTime())) {
                    prev = shortlist[i];
                }
            }

            if(prev == null || prevDropLocation.start.valueOf() < prev.start.getTime() ||
                prevDropLocation.start.valueOf() + 3600000 > prev.start.getTime() + 86400000) {
                $(this).addClass('invalid');
                return true;
            }
        }

        $(this).removeClass('invalid');

    }

    function initialize() {
        var plans = $('#plan');
        if (isInitialized)
            return;
        isInitialized = true;

        // find min an max time
        var localOffset = (3600000 * 7) - (new Date()).getTimezoneOffset() * 60000;
        for (var i = 0; i < window.planEvents.length; i++) {
            var s = new Date(new Date(window.planEvents[i].start) - localOffset),
                e = new Date(new Date(window.planEvents[i].end) - localOffset);
            window.planEvents[i].start = s;
            window.planEvents[i].end = e;
        }

        var origExternalDrag = $.fullCalendar.Grid.prototype.startExternalDrag,
            prevDragged,
            origRenderDrag = $.fullCalendar.View.prototype.renderDrag;

        $.fullCalendar.View.prototype.renderDrag = function (dropLocation, seg) {
            prevDropLocation = dropLocation;
            if(prevDragged.is('.fc-event.event-type-p')) {
                var next = null;
                // find the next occurring event of the same class, must come before that.
                for(var i = 0; i < shortlist.length; i++) {
                    if(shortlist[i].start.getTime() > dropLocation.start.valueOf() &&
                        (next == null || shortlist[i].start.getTime() < next.start.getTime())) {
                        next = shortlist[i];
                    }
                }

                if(next != null) {
                    this.renderHighlight(
                        this.view.calendar.ensureVisibleEventRange({
                            start: moment(new Date(next.start.getTime() - 86400000)),
                            end: moment(new Date(next.start.getTime()))}) // needs to be a proper range
                    );
                }
            }
            else if(prevDragged.is('.fc-event.event-type-sr')) {
                var prev = null;
                // find the prev occurring event of the same class, must come before that.
                for(var i = 0; i < shortlist.length; i++) {
                    if(shortlist[i].start.getTime() < dropLocation.start.valueOf() &&
                        (prev == null || shortlist[i].start.getTime() > prev.start.getTime())) {
                        prev = shortlist[i];
                    }
                }

                if(prev != null) {
                    this.renderHighlight(
                        this.view.calendar.ensureVisibleEventRange({
                            start: moment(new Date(prev.start.getTime())),
                            end: moment(new Date(prev.start.getTime() + 86400000))}) // needs to be a proper range
                    );
                }
            }
            else
                origRenderDrag.apply(this, [dropLocation, seg]);
        };

        $.fullCalendar.Grid.prototype.startExternalDrag = function (el, ev, ui) {
            var classI = (/class([0-9])(\s|$)/ig).exec(el.attr('class'));
            if(classI != null) {
                shortlist = window.planEvents.filter(function (e) {
                    return e.className.indexOf('class' + classI[1]) > -1 && e.className.indexOf('event-type-c') > -1;
                });
            }
            else {
                shortlist = [];
            }
            prevDragged = el;
            if(this.renderDrag != $.fullCalendar.View.prototype.renderDrag)
                this.renderDrag = $.fullCalendar.View.prototype.renderDrag;
            // stupid dropAccept option should be used when dropping not with initiating
            var originalExternalDrop = this.view.reportExternalDrop;
            this.view.reportExternalDrop = function (meta, dropLocation, el, ev, ui) {
                if(el.is('.invalid'))
                    return false;
                originalExternalDrop.apply(this, [meta, dropLocation, el, ev, ui]);
            };
            origExternalDrag.apply(this, [el, ev, ui]);
        };

        calendar = $('#calendar').fullCalendar({
            //minTime: '06:00:00',
            //maxTime: '26:00:00',
            titleFormat: 'MMMM',
            editable: true,
            draggable: true,
            droppable: true, // this allows things to be dropped onto the calendar
            aspectRatio: 1.9,
            timezone: 'local',
            businessHours: true,
            timeslotsPerHour: 4,
            slotEventOverlap: false,
            slotMinutes: 15,
            firstHour: new Date().getHours(),
            eventRender: function (event, element) {
                element.find('.fc-title').html(event.title);
                return true;
            },
            header: {
                left: 'prev,next today',
                center: '',
                right: 'agendaDay,agendaWeek,month'
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
            drop: function(date, jsEvent, ui) {
                // TODO: count down to remove with numbers in event
                if(!$(this).is('.invalid'))
                    $(this).remove();
            },
            eventClick: function (event, jsEvent, view) {
                // change the border color just for fun
                if (plans.find('.event-id-' + event.eventId).length > 0) {
                    if (!plans.find('.event-id-' + event.eventId).is('.selected'))
                        plans.find('.event-id-' + event.eventId).find('.assignment').trigger('click');
                    plans.find('.event-id-' + event.eventId).scrollintoview(DASHBOARD_MARGINS);
                }

                var plan = $('#plan');
                plan.addClass('session-selected');
                $('#calendar').fullCalendar('gotoDate', event.start);
                $('#calendar').fullCalendar('changeView', 'agendaDay');

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

                if(shouldRevert.apply(this)) {
                    revertFunc();
                }

            },
            views: {
                month: { // name of view
                    titleFormat: 'MMM DD'
                    // other view-specific options here
                }
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

    body.on('click', '#plan-step-3 a[href="#add-prework"]', function () {
        $('#plan').addClass('add-events');
        var external = $('#external-events');
        external.find('.fc-event').remove();
        external.find('h4').text('Pre-work');
        $('#plan-step-1').find('h4').each(function () {
            var event = $('<div class="fc-event ui-draggable ui-draggable-handle event-type-p ' + $(this).find('span').attr('class') +
                '"><div class="fc-title"><h4>P</h4>' + $(this).text().trim() +
                '</div></div>').insertAfter(external.find('> h4'));

            // store data so the calendar knows to render an event upon drop
            event.data('event', {
                title: $.trim(event.find('.fc-title').html()), // use the element's text as the event title
                allDay: false,
                className: 'event-type-p ' + $(this).find('span').attr('class'),
                editable: true,
                overlap:false,
                stick: true // maintain when user navigates (see docs on the renderEvent method)
            });

            // make the event draggable using jQuery UI
            event.draggable({
                zIndex: 999,
                revert: shouldRevert,      // will cause the event to go back to its
                revertDuration:.15  //  original position after the drag
            });
        });

        body.one('click', '#plan a[href="#save-plan"]', function (evt) {
            evt.preventDefault();
            $('#plan').removeClass('add-events');
            $('#plan-step-32').modal({show: true})
        });
    });

    body.on('click', '#plan-step-32 a[href="#add-spaced-repetition"]', function () {
        $('#plan').addClass('add-events');

        var external = $('#external-events');
        external.find('.fc-event').remove();
        external.find('h4').text('Spaced-repetition');
        $('#plan-step-1').find('h4').each(function () {
            var event = $('<div class="fc-event ui-draggable ui-draggable-handle event-type-sr ' + $(this).find('span').attr('class') +
            '"><div class="fc-title"><h4>SR</h4>' + $(this).text().trim() +
            '</div></div>').insertAfter(external.find('> h4'));

            // store data so the calendar knows to render an event upon drop
            event.data('event', {
                title: $.trim(event.find('.fc-title').html()), // use the element's text as the event title
                allDay: false,
                className: 'event-type-sr ' + $(this).find('span').attr('class'),
                editable: true,
                overlap:false,
                stick: true // maintain when user navigates (see docs on the renderEvent method)
            });

            // make the event draggable using jQuery UI
            event.draggable({
                zIndex: 999,
                revert: shouldRevert,      // will cause the event to go back to its
                revertDuration:.15  //  original position after the drag
            });
        });

        body.one('click', '#plan a[href="#save-plan"]', function (evt) {
            evt.preventDefault();
            $('#plan').removeClass('add-events');
            $('#plan-step-33').modal({show: true})
        });
    });

    body.on('click', '#plan-step-33 a[href="#add-free-study"]', function () {
        $('#plan').addClass('add-events');

        var external = $('#external-events');
        external.find('.fc-event').remove();
        external.find('h4').text('Free study');
        $('#plan-step-1').find('h4').each(function () {
            var event = $('<div class="fc-event ui-draggable ui-draggable-handle event-type-f"><div class="fc-title"><h4>F</h4>Free study</div></div>').insertAfter(external.find('> h4'));

            // store data so the calendar knows to render an event upon drop
            event.data('event', {
                title: $.trim(event.find('.fc-title').html()), // use the element's text as the event title
                allDay: false,
                className: 'event-type-f',
                editable: true,
                overlap:false,
                stick: true // maintain when user navigates (see docs on the renderEvent method)
            });

            // make the event draggable using jQuery UI
            event.draggable({
                zIndex: 999,
                revert: true,      // will cause the event to go back to its
                revertDuration:.15  //  original position after the drag
            });
        });

        body.one('click', '#plan a[href="#save-plan"]', function (evt) {
            evt.preventDefault();
            $('#plan').removeClass('add-events');
            $('#plan-step-4').modal({show: true})
        });
    });

    body.on('click', '#plan a[href="#add-note"]', function (evt) {
        evt.preventDefault();
        var plan = $('#plan');
        plan.removeClass('session-selected').addClass('edit-note');
        plan.find('#editor2').focus();
        var strategy = plan.find('[name="strategy-select"]').val();
        CKEDITOR.instances.editor2.setData(plan.find('.strategy-' + strategy).html());
    });

    body.on('hide', '#plan', function () {
        if(typeof CKEDITOR.instances.editor2 != 'undefined')
            CKEDITOR.instances.editor2.fire('blur');
        $('#cke_editor2').hide();
        var notes = $('#plan');
        if(notes.is('.edit-note')) {
            notes.find('a[href="#save-note"]').trigger('click');
        }
    });

    function initializeCKE()
    {
        var notes = $('#plan');
        if(typeof CKEDITOR.instances.editor2 == 'undefined' ||
            typeof CKEDITOR.instances.editor2.setReadOnly == 'undefined' ||
            typeof CKEDITOR.instances.editor2.editable() == 'undefined') {
            setTimeout(initializeCKE, 100);
            return;
        }
        var editor = CKEDITOR.instances.editor2;
        editor.on('blur',function( e ){
            if(notes.is('.edit-note') && notes.is(':visible'))
                editor.fire('focus');
        });
        editor.on('focus',function( e ){
            CKEDITOR.instances.editor2.setReadOnly(false);
            var cke = $('#cke_editor2'),
                edit = $('#editor2');
            if(cke.width() != edit.outerWidth()) {
                cke.width(edit.outerWidth());
                if(notes.is('.edit-note')) {
                    editor.fire('blur');
                    editor.fire('focus');
                }
            }
        });
        editor.setReadOnly(false);
    }

    // The calendar needs to be in view for sizing information.  This will not initialize when display:none;, so instead
    //   we will activate the calendar only once, when the menu is clicked
    body.on('show', '#plan', function () {
        setupPlan();

        var notes = $('#notes');
        // load editor
        if(!$(this).is('.loaded')) {
            $(this).addClass('loaded');

            setTimeout(initializeCKE, 100);
        }
    });

    $(window).resize(function () {
        $('#cke_editor2').width($('#editor2').outerWidth());
        if(typeof CKEDITOR.instances.editor2 != 'undefined')
            CKEDITOR.instances.editor2.fire('resize');
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