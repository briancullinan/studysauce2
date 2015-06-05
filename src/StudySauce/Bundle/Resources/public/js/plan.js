$(document).ready(function () {

    var body = $('body'),
        isInitialized = false,
        calendar, notebookId;

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

    var clickTimeout;
    body.on('dblclick', '#plan #calendar .fc-event', function () {
    });

    body.on('click', '#plan .fc-agendaDay-button', function () {
        var plan = $('#plan');
        plan.addClass('session-selected');
        plan.setClock();
    });

    body.on('click', '#plan .fc-agendaWeek-button, #plan .fc-month-button', function () {
        $('#plan').removeClass('session-selected');
    });

    var prevDropLocation, shortlist;
    function shouldRevert() {

        if(prevDropLocation == null)
            return true;

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
            if(typeof seg != 'undefined')
                prevDragged = seg.el;
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
                    this.renderSelection(
                        this.calendar.ensureVisibleEventRange({
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
                    this.renderSelection(
                        this.calendar.ensureVisibleEventRange({
                            start: moment(new Date(prev.end.getTime())),
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
            this.renderDrag = function (dropLocation, seg) {
                $.fullCalendar.View.prototype.renderDrag.apply(this.view, [dropLocation, seg]);
            };
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
            views: {
                day: {
                    columnFormat: 'dddd D MMMM'
                },
                month: {
                    columnFormat: 'dddd, MMMM'
                }
            },
            height: 600,
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
            viewRender: function( view, element )
            {
                if(this.renderDrag != $.fullCalendar.View.prototype.renderDrag)
                    this.renderDrag = $.fullCalendar.View.prototype.renderDrag;
                // stupid dropAccept option should be used when dropping not with initiating
                var originalExternalDrop = this.reportExternalDrop;
                this.reportExternalDrop = function (meta, dropLocation, el, ev, ui) {
                    if(el.is('.invalid'))
                        return false;
                    originalExternalDrop.apply(this, [meta, dropLocation, el, ev, ui]);
                };
            },
            eventRender: function (event, element) {
                element.data('event', event);
                element.addClass('event-id-' + event.eventId);
                element.find('.fc-title').html(event.title);
                element.bind('dblclick', function () {
                    clearTimeout(clickTimeout);
                    setTimeout(function () {
                        clearTimeout(clickTimeout);
                        var dialog = $('#edit-event');
                        if(event.className.indexOf('event-type-c') > -1) {
                            dialog.addClass('class-only');
                            dialog.find('.title').addClass('read-only');
                        }
                        else {
                            dialog.removeClass('class-only');
                            dialog.find('.title').removeClass('read-only');
                            dialog.find('.start-time input.is-timeEntry').timeEntry('setTime', event.start.toDate());
                            dialog.find('.end-time input.is-timeEntry').timeEntry('setTime', event.end.toDate());
                            dialog.find('.day-of-the-week input:checked').prop('checked', false);
                            dialog.find('.day-of-the-week .checkbox:nth-child(' + event.start.isoWeekday() + ') input').prop('checked', true);
                            // find the earliest occurence of this event
                            var start = Math.min.apply(null, window.planEvents.map(function (e) {return e.start;}));
                            var end = Math.min.apply(null, window.planEvents.map(function (e) {return e.end;}));
                            dialog.find('.start-date input[type="text"]').datepicker('setDate', start);
                            dialog.find('.end-date input[type="text"]').datepicker('setDate', end);
                            dialog.find('.title input').val(event.title.replace(/<h4>[^<]*<\/h4>/, ''));
                        }
                        var type = (/event-type-([a-z]*)(\s|$)/ig).exec(event.className.join(' '))[1];
                        var alert = $('#plan-step-3').find('[name="event-type-' + type + '"][value="0"]:checked, select[name="event-type-' + type + '"]').first().val();
                        dialog.find('.reminder select').val(alert);
                        dialog.modal({show:true});
                    }, 300);
                });
                return true;
            },
            header: {
                left: 'prev,next today agendaDay,agendaWeek,month',
                center: '',
                right: ''
            },
            defaultView: plans.is('.setup-mode') ? 'agendaWeek' : 'agendaDay',
            selectable: false,
            events: window.planEvents,
            drop: function(date, jsEvent, ui) {
                // TODO: count down to remove with numbers in event
                if(!$(this).is('.invalid'))
                    $(this).remove();
            },
            eventClick: function (event, jsEvent, view) {
                var plan = $('#plan');
                if(body.is('.adviser'))
                    return;
                var classI = (/class([0-9])(\s|$)/ig).exec($(this).attr('class'));
                if(!plan.is('.setup-mode')) {
                    clickTimeout = setTimeout(function () {
                        calendar.fullCalendar('gotoDate', event.start);
                        calendar.fullCalendar('changeView', 'agendaDay');
                        plan.addClass('session-selected');
                        // change mini checkin color
                        var notes;
                        if(typeof event.courseId != 'undefined') {
                            notebookId = event.courseId;
                            plan.find('.mini-checkin').show();
                            plan.find('.mini-checkin a').attr('href', '#class' + classI[1]);
                            plan.find('.mini-checkin a').attr('class', 'checkin ' + classI[0] + ' course-id-' + event.courseId);

                            // show related notes
                            notes = $('#notes').find('.class-row.course-id-' + event.courseId).find('~ .notes .note-row');
                        }
                        else {
                            notebookId = '';
                            plan.find('.mini-checkin').hide();
                        }

                        if(notes.length == 0) {
                            notes = $('#notes').find('.note-row');
                        }
                        plan.find('.session-strategy .note-row').remove();
                        notes.slice(0, Math.min(8, notes.length)).clone().appendTo(plan.find('.session-strategy'));

                        // highlight selected event
                        plan.find('.event-selected').removeClass('event-selected');
                        plan.find('#calendar .event-id-' + event.eventId).addClass('event-selected');

                        // set the title
                        plan.find('h2').text(event.title
                            .replace(/<h4>C<\/h4>/, 'Class: ')
                            .replace(/<h4>F<\/h4>/, 'Free study: ')
                            .replace(/<h4>D<\/h4>/, 'Deadline: ')
                            .replace(/<h4>P<\/h4>/, 'Pre-work: ')
                            .replace(/<h4>SR<\/h4>/, 'Study session: ')
                            .replace(/<h4>D<\/h4>/, 'Deadlines: '));
                        // set the template for notes
                        if(event.className.indexOf('event-type-p') > -1)
                            plan.find('[name="strategy-select"]').val('prework');
                        else if(typeof event.courseId != 'undefined') {
                            var type = $('#plan-step-4').find('[name="profile-type-' + event.courseId + '"]').val();
                            if(type == 'memorization') {
                                plan.find('[name="strategy-select"]').val('spaced');
                            }
                            else if(type == 'reading') {
                                plan.find('[name="strategy-select"]').val('active');
                            }
                            else if(type == 'conceptual') {
                                plan.find('[name="strategy-select"]').val('teach');
                            }
                        }
                        else
                            plan.find('[name="strategy-select"]').val('spaced');
                        plan.setClock();
                    }, 300);
                }
            },
            eventDragStart: function (event) {
                var classI = (/class([0-9])(\s|$)/ig).exec($(this).attr('class'));
                if(classI != null) {
                    shortlist = window.planEvents.filter(function (e) {
                        return e.className.indexOf('class' + classI[1]) > -1 && e.className.indexOf('event-type-c') > -1;
                    });
                }
                else {
                    shortlist = [];
                }
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

                $.ajax({
                    url: window.callbackPaths['plan_update'],
                    type: 'POST',
                    dataType: 'text',
                    data: {
                        eventId: event['eventId'],
                        start: event['start'].toJSON(),
                        end: event['end'].toJSON()
                    },
                    error: revertFunc,
                    success: function (data) {
                        var content = $(data);
                        body.addClass('download-plan');
                        window.planEvents = [];
                        // merge scripts
                        ssMergeScripts(content.filter('script:not([src])'));
                        for (var j = 0; j < window.planEvents.length; j++) {
                            window.planEvents[j].start = new Date(window.planEvents[j].start);
                            window.planEvents[j].end = new Date(window.planEvents[j].end);
                        }
                        if (calendar != null && typeof calendar.fullCalendar != 'undefined')
                            calendar.fullCalendar('refetchEvents');
                    }
                });
            },
            eventReceive: function () {

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

        if(!plan.is('.setup-mode') || $('#plan-step-0, #plan-step-1, #plan-step-2, #plan-step-3, #plan-step-4, #plan-step-5, #plan-step-6').first().modal({
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

    body.on('click', '#plan-step-2 a[href="#add-prework"]', function () {
        $('#plan').addClass('add-events');
        var external = $('#external-events');
        external.find('.fc-event').remove();
        external.find('h4').text('Pre-work');
        $('#plan-step-1').find('h4').each(function () {
            var courseId = (/course-id-([0-9]*)(\s|$)/ig).exec($(this).find('span').attr('class'))[1];
            var difficulty = $(this).nextAll('.radio').find(':checked').val();
            // skip none events
            if(difficulty == 'none')
                return true;
            var length = difficulty == 'easy' ? '00:45' : (difficulty == 'tough' ? '02:00' : '01:00');
            var count = parseInt($(this).attr('data-reoccurs'));
            for(var i = 0; i < count; i++) {
                var event = $('<div class="fc-event ui-draggable ui-draggable-handle event-type-p ' + $(this).find('span').attr('class') +
                '" data-event="1" data-duration="' + length + '"><div class="fc-title"><h4>P</h4>' + $(this).text().trim() +
                '</div></div>').insertBefore(external.find('.highlighted-link'));

                // store data so the calendar knows to render an event upon drop
                event.data('event', {
                    courseId: courseId,
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
            }
        });

        body.one('click', '#plan a[href="#save-plan"]', function (evt) {
            evt.preventDefault();
            var events = $('#calendar').fullCalendar('clientEvents'),
                studyEvents = [];
            for(var i = 0; i < events.length; i++) {
                if(events[i].className.indexOf('event-type-p') > -1 && typeof events[i].eventId == 'undefined') {
                    studyEvents[studyEvents.length] = {
                        type: 'p',
                        start: events[i].start.toDate().toJSON(),
                        end: events[i].end.toDate().toJSON(),
                        courseId: events[i].courseId
                    };
                }
            }
            $('#plan').removeClass('add-events');
            $('#plan-step-2-2').modal({show: true});
            $.ajax({
                url: window.callbackPaths['plan_create'],
                type: 'POST',
                dataType: 'json',
                data: {
                    events: studyEvents
                },
                success: function (data) {

                }
            });
        });
    });

    body.on('click', '#plan-step-2-2 a[href="#add-spaced-repetition"]', function () {
        $('#plan').addClass('add-events');

        var external = $('#external-events');
        external.find('.fc-event').remove();
        external.find('h4').text('Spaced-repetition');
        $('#plan-step-1').find('h4').each(function () {
            var courseId = (/course-id-([0-9]*)(\s|$)/ig).exec($(this).find('span').attr('class'))[1];
            var difficulty = $(this).nextAll('.radio').find(':checked').val();
            // skip none events
            if(difficulty == 'none')
                return true;
            var length = difficulty == 'easy' ? '00:45' : (difficulty == 'tough' ? '02:00' : '01:00');
            var count = parseInt($(this).attr('data-reoccurs'));
            for(var i = 0; i < count; i++) {
                var event = $('<div class="fc-event ui-draggable ui-draggable-handle event-type-sr ' + $(this).find('span').attr('class') +
                '" data-event="1" data-duration="' + length + '"><div class="fc-title"><h4>SR</h4>' + $(this).text().trim() +
                '</div></div>').insertBefore(external.find('.highlighted-link'));

                // store data so the calendar knows to render an event upon drop
                event.data('event', {
                    courseId: courseId,
                    title: $.trim(event.find('.fc-title').html()), // use the element's text as the event title
                    allDay: false,
                    className: 'event-type-sr ' + $(this).find('span').attr('class'),
                    editable: true,
                    overlap: false,
                    stick: true // maintain when user navigates (see docs on the renderEvent method)
                });

                // make the event draggable using jQuery UI
                event.draggable({
                    zIndex: 999,
                    revert: shouldRevert,      // will cause the event to go back to its
                    revertDuration: .15  //  original position after the drag
                });
            }
        });

        body.one('click', '#plan a[href="#save-plan"]', function (evt) {
            evt.preventDefault();
            var events = $('#calendar').fullCalendar('clientEvents'),
                studyEvents = [];
            for(var i = 0; i < events.length; i++) {
                if(events[i].className.indexOf('event-type-sr') > -1 && typeof events[i].eventId == 'undefined') {
                    studyEvents[studyEvents.length] = {
                        type: 'sr',
                        start: events[i].start.toDate().toJSON(),
                        end: events[i].end.toDate().toJSON(),
                        courseId: events[i].courseId
                    };
                }
            }
            $('#plan').removeClass('add-events');
            $('#plan-step-2-3').modal({show: true});
            $.ajax({
                url: window.callbackPaths['plan_create'],
                type: 'POST',
                dataType: 'json',
                data: {
                    events: studyEvents
                },
                success: function (data) {

                }
            });
        });
    });

    body.on('change', '#plan-step-3 input', function () {
        var dialog = $('#plan-step-3');
        if(dialog.find('input[name="' + $(this).attr('name') + '"][value="0"]:checked').length > 0) {
            $(this).parents('.radio').find('~ .input').first().css('visibility', 'hidden');
        }
        else {
            $(this).parents('.radio').find('~ .input').first().css('visibility', 'visible');
        }
    });

    body.on('click', '#plan-step-2-3 a[href="#add-free-study"]', function () {
        $('#plan').addClass('add-events');

        var external = $('#external-events');
        external.find('.fc-event').remove();
        external.find('h4').text('Free study');
        for(var i = 0; i < 15; i++) {

            var event = $('<div class="fc-event ui-draggable ui-draggable-handle event-type-f" data-event="1" data-duration="01:00"><div class="fc-title"><h4>F</h4>Free study</div></div>').insertAfter(external.find('> h4'));

            // store data so the calendar knows to render an event upon drop
            event.data('event', {
                title: $.trim(event.find('.fc-title').html()), // use the element's text as the event title
                allDay: false,
                className: 'event-type-f',
                editable: true,
                overlap: false,
                stick: true // maintain when user navigates (see docs on the renderEvent method)
            });

            // make the event draggable using jQuery UI
            event.draggable({
                zIndex: 999,
                revert: true,      // will cause the event to go back to its
                revertDuration: .15  //  original position after the drag
            });
        }
        $('<p>Drag as many as you like</p>').insertAfter(external.find('> h4'));

        body.one('click', '#plan a[href="#save-plan"]', function (evt) {
            evt.preventDefault();
            var events = $('#calendar').fullCalendar('clientEvents'),
                studyEvents = [];
            for(var i = 0; i < events.length; i++) {
                if(events[i].className.indexOf('event-type-f') > -1 && typeof events[i].eventId == 'undefined') {
                    studyEvents[studyEvents.length] = {
                        type: 'f',
                        start: events[i].start.toDate().toJSON(),
                        end: events[i].end.toDate().toJSON()
                    };
                }
            }
            $('#plan').removeClass('add-events');
            $('#plan-step-3').modal({show: true});
            $.ajax({
                url: window.callbackPaths['plan_create'],
                type: 'POST',
                dataType: 'json',
                data: {
                    events: studyEvents
                },
                success: function (data) {

                }
            });
        });
    });

    body.on('click', '#plan-step-5 a[href="#make-final"]', function () {
        var plan = $('#plan');
        var external = $('#external-events');
        plan.addClass('add-events');
        plan.find('a[href="#save-plan"]').text('Done');
        external.find('.fc-event, p').remove();
        external.find('h4').text('Final adjustments');
        body.one('click', '#plan a[href="#save-plan"]', function (evt) {
            evt.preventDefault();
            $('#plan').removeClass('add-events');
            $('#plan-step-6').modal({show: true})
        });
    });

    body.on('click', '#plan-step-6 a[href*="/plan/download"]', function (evt) {
        $('#plan-step-6').find('.highlighted-link').removeClass('invalid').addClass('valid');
    });

    body.on('click', '#plan-step-6 a[href="#done"]', function () {
        var plan = $('#plan'),
            dialog = $('#plan-step-6');
        if(dialog.find('.highlighted-link').is('.invalid')) {
            dialog.addClass('invalid-only');
            return;
        }
        plan.setClock();
        calendar.fullCalendar('changeView', 'agendaDay');
        plan.removeClass('setup-mode').addClass('session-selected');
    });

    body.on('click', '#plan a[href="#add-note"]', function (evt) {
        evt.preventDefault();
        var plan = $('#plan');
        noteId = '';
        tags = [];
        plan.removeClass('session-selected').addClass('edit-note');
        plan.find('#editor2').focus();
        var strategy = plan.find('[name="strategy-select"]').val();
        CKEDITOR.instances.editor2.setData(plan.find('.strategy-' + strategy).html());
    });

    var noteId, tags;
    body.on('click', '#plan .note-row', function (evt) {
        evt.preventDefault();
        var plan = $('#plan'),
            note = $(this);
        tags = JSON.parse(note.attr('data-tags')) || [];
        noteId = (/note-id-([a-z0-9\-]*)(\s|$)/ig).exec($(this).attr('class'))[1];
        notebookId = (/notebook-id-([a-z0-9\-]*)(\s|$)/ig).exec($(this).attr('class'))[1];
        plan.removeClass('session-selected').addClass('edit-note');
        CKEDITOR.instances.editor2.setData(note.find('.summary en-note').html());
        plan.find('.highlighted-link').removeClass('valid').addClass('invalid');
        $.ajax({
            url: window.callbackPaths['notes_note'],
            type: 'GET',
            dataType: 'text',
            data: {
                noteId: noteId
            },
            success: function (data) {
                CKEDITOR.instances.editor2.setData($(data).filter('en-note').html());
                plan.find('.highlighted-link').removeClass('invalid').addClass('valid');
            }
        });
        setTimeout(function () {
            if(typeof CKEDITOR.instances.editor2 != 'undefined')
                CKEDITOR.instances.editor2.fire('focus');
        }, 20);
    });

    body.on('click', '#plan a[href="#save-note"]', function (evt) {
        evt.preventDefault();
        var plan = $('#plan');
        plan.find('.highlighted-link').removeClass('valid').addClass('invalid');
        loadingAnimation($(this));
        $.ajax({
            url: window.callbackPaths['notes_update'],
            type: 'POST',
            dataType: 'text',
            data: {
                noteId: noteId,
                tags: tags.join(','),
                title: plan.find('.pane-content > h2').text(),
                notebookId: notebookId,
                body: CKEDITOR.instances['editor2'].getData()
            },
            success: function (data) {
                plan.find('.squiggle').remove();
                plan.removeClass('edit-note').addClass('session-selected').find('.highlighted-link').removeClass('invalid').addClass('valid');
                $('#editor2').blur();
                CKEDITOR.instances.editor2.fire('blur');
            },
            error: function () {
                plan.find('.squiggle').remove();
            }
        });
    });

    function editEventFunc()
    {
        var dialog = $('#edit-event');
        if(dialog.find('.day-of-the-week input:checked').length == 0) {
            dialog.addClass('dotw-required');
        }
        else {
            dialog.removeClass('dotw-required');
        }
        if(dialog.find('.start-time input').val().trim() == '') {
            dialog.addClass('start-time-required');
        }
        else {
            dialog.removeClass('start-time-required');
        }
        if(dialog.find('.end-time input').val().trim() == '') {
            dialog.addClass('end-time-required');
        }
        else {
            dialog.removeClass('end-time-required');
        }
        if(dialog.find('.start-date input').val().trim() == '') {
            dialog.addClass('start-date-required');
        }
        else {
            dialog.removeClass('start-date-required');
        }
        if(dialog.find('.end-date input').val().trim() == '') {
            dialog.addClass('end-date-required');
        }
        else {
            dialog.removeClass('end-date-required');
        }

        // check for invalid time entry
        var from = dialog.find('.start-time input.is-timeEntry').timeEntry('getTime'),
            to = dialog.find('.end-time input.is-timeEntry').timeEntry('getTime');
        if(from != null && to != null) {
            var length = (to.getTime() - from.getTime()) / 1000;
            if (length < 0)
                length += 86400;
            // check if the length is less than 12 hours
            if (from.getTime() == to.getTime() || length > 12 * 60 * 60)
                dialog.addClass('invalid-time');
            else
                dialog.removeClass('invalid-time');
        }

        // check if there are any overlaps with the other rows
        var startDate = new Date(dialog.find('.start-date input.hasDatepicker').val());
        var endDate = new Date(dialog.find('.end-time input.hasDatepicker').val());

        // check if dates are reverse
        if(!isNaN(startDate.getTime()) && !isNaN(endDate.getTime()) && startDate.getTime() > endDate.getTime()) {
            dialog.addClass('invalid-date');
        }
        else {
            dialog.removeClass('invalid-date')
        }

        if(dialog.is('.class-required') || dialog.is('.dotw-required') || dialog.is('.start-time-required') ||
            dialog.is('.end-time-required') || dialog.is('.start-date-required') || dialog.is('.end-date-required') ||
            dialog.is('.invalid-date') || dialog.is('.invalid-time'))
            dialog.find('.highlighted-link').removeClass('valid').addClass('invalid');
        else
            dialog.removeClass('invalid-only').find('.highlighted-link').removeClass('invalid').addClass('valid');
    }

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
    }
    body.on('change', '#edit-event .start-time input, #edit-event .end-time input, ' +
    '#edit-event .start-date input, #edit-event .end-date input', copyTimes);
    body.on('blur', '#edit-event .start-time input, #edit-event .end-time input, ' +
    '#edit-event .start-date input, #edit-event .end-date input', copyTimes);
    body.on('keyup', '#edit-event .class-name input, #edit-event .start-time input, #edit-event .end-time input, ' +
    '#edit-event .start-date input, #edit-event .end-date input, #edit-event .university input', editEventFunc);
    body.on('focus', '#edit-event .start-time input[type="time"], #edit-event .end-time input[type="time"]', function () {
        if($(this).val() == '')
            $(this).val('12:00:00');
    });
    body.on('change', '#edit-event .start-date input[type="text"], #edit-event .end-date input[type="text"]', function () {
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
    body.on('change', '#edit-event .class-name input, #edit-event .day-of-the-week input, #edit-event .start-time input, ' +
    '#edit-event .end-time input, #edit-event .start-date input, #edit-event .end-date input, #edit-event .university input', editEventFunc);

    body.on('show.bs.modal', '#edit-event', editEventFunc);

    body.on('click', '#edit-event [type="submit"]', function (evt) {
        evt.preventDefault();
        body.addClass('download-plan');
        var dialog = $('#edit-event');
        if(dialog.find('.highlighted-link').is('.invalid')) {
            dialog.addClass('invalid-only');
            return false;
        }
        dialog.find('.highlighted-link').removeClass('valid').addClass('invalid');
        loadingAnimation(dialog.find('[type="submit"]'));
        
        $.ajax({
            url: window.callbackPaths['plan_update'],
            type: 'POST',
            dataType: 'text',
            data: {
                eventId: event['eventId'],
                start: event['start'].toJSON(),
                end: event['end'].toJSON()
            },
            success: function (data) {
                dialog.find('.squiggle').remove();
                var content = $(data);
                body.addClass('download-plan');
                window.planEvents = [];
                // merge scripts
                ssMergeScripts(content.filter('script:not([src])'));
                for (var j = 0; j < window.planEvents.length; j++) {
                    window.planEvents[j].start = new Date(window.planEvents[j].start);
                    window.planEvents[j].end = new Date(window.planEvents[j].end);
                }
                if (calendar != null && typeof calendar.fullCalendar != 'undefined')
                    calendar.fullCalendar('refetchEvents');
            }
        });
    });

    // The calendar needs to be in view for sizing information.  This will not initialize when display:none;, so instead
    //   we will activate the calendar only once, when the menu is clicked
    body.on('show', '#plan', function () {
        setupPlan();

        var notes = $('#notes');

        var editEvent = $('#edit-event');
        editEvent.find('.start-date input[type="text"], .end-date input[type="text"]')
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

        editEvent.find('.start-time input[type="text"]:not(.is-timeEntry), .end-time input[type="text"]:not(.is-timeEntry)')
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
        var customization = $('#plan-step-1:visible'),
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

    body.on('change', '#plan-step-1 input, #plan-step-4 input', step1Func);
    body.on('show', '#plan-step-1, #plan-step-4', step1Func);
    body.on('submit', '#plan-step-1 form', submitStep1);
    body.on('shown.bs.modal', '#plan-step-1, #plan-step-4', step1Func);

    function step4Func() {
        var customization = $('#plan-step-4'),
            valid = true;

        customization.find('select').each(function () {
            var inputSet = $(this);
            if(inputSet.val().trim().length == 0) {
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


    function submitStep4(evt)
    {
        evt.preventDefault();
        var customization = $('#plan-step-4');
        if(customization.find('.highlighted-link').is('.invalid')) {
            customization.addClass('invalid-only');
            customization.find('.type-required').first().nextAll('label').find('select').first().focus();
            return;
        }
        customization.find('.highlighted-link').removeClass('valid').addClass('invalid');
        loadingAnimation($(this).find('[value="#save-profile"]'));
        var scheduleData = { };
        customization.find('select').each(function () {
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

    body.on('change', '#plan-step-4 select', step4Func);
    body.on('show', '#plan-step-4', step4Func);
    body.on('submit', '#plan-step-4 form', submitStep4);
    body.on('shown.bs.modal', '#plan-step-4', step4Func);


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