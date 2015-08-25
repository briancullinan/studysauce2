$(document).ready(function () {

    var body = $('body'),
        isInitialized = false,
        calendar, clickTimeout;

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

        if(prevDropLocation == null || prevDropLocation.allDay == true || body.is('.fc-not-allowed')) {
            $(this).addClass('invalid');
            return true;
        }

        var length = 3600000;
        if(typeof $(this).attr('data-duration') != 'undefined') {
            var parts = $(this).attr('data-duration').split(':');
            length = (parseInt(parts[0]) * 60 + parseInt(parts[1])) * 60 * 1000;
        }

        if($(this).is('.event-type-f')) {
            var s = new Date(prevDropLocation.start.valueOf());
            s.setHours(0, 0, 0, 0);
            s = s.getTime();
            // find a study session already in the space
            var skip = false;
            for(var j = 0; j < shortlist.length; j++) {
                if(shortlist[j].className.indexOf('event-type-f') > -1 &&
                    shortlist[j].start.valueOf() >= s &&
                    shortlist[j].start.valueOf() <= s + 86400000) {
                    // cancel highlighting
                    skip = true;
                }
            }
            if(skip) {
                $(this).addClass('invalid');
                return true;
            }
        }
        if($(this).is('.event-type-p')) {
            var next = null;
            // find the next occurring event of the same class, must come before that.
            for(var i = 0; i < shortlist.length; i++) {
                if(shortlist[i].className.indexOf('event-type-c') > -1 &&
                    shortlist[i].start.valueOf() > prevDropLocation.start.valueOf() &&
                    (next == null || shortlist[i].start.valueOf() < next.start.valueOf())) {
                    next = shortlist[i];
                }
            }

            // find a study session already in the space
            for(var i = 0; i < shortlist.length; i++) {
                if(next != null && shortlist[i].className.indexOf('event-type-p') > -1 &&
                    shortlist[i].start.valueOf() <= next.start.valueOf() &&
                    shortlist[i].start.valueOf() >= next.start.valueOf() - 86400000) {
                    // cancel highlighting
                    next = null;
                }
            }

            if(next == null || prevDropLocation.start.valueOf() + length > next.start.valueOf() ||
                prevDropLocation.start.valueOf() < next.start.valueOf() - 86400000) {
                $('#plan-science').modal({show: true});
                $(this).addClass('invalid');
                return true;
            }
        }
        else if($(this).is('.event-type-sr'))
        {
            var prev = null;
            // find the prev occurring event of the same class, must come before that.
            for(var i = 0; i < shortlist.length; i++) {
                if(shortlist[i].className.indexOf('event-type-c') > -1 &&
                    shortlist[i].start.valueOf() < prevDropLocation.start.valueOf() &&
                    (prev == null || shortlist[i].start.valueOf() > prev.start.valueOf())) {
                    prev = shortlist[i];
                }
            }

            // find a study session already in the space
            for(var i = 0; i < shortlist.length; i++) {
                if(prev != null && shortlist[i].className.indexOf('event-type-sr') > -1 &&
                    shortlist[i].start.valueOf() <= prev.start.valueOf() + 86400000 &&
                    shortlist[i].start.valueOf() >= prev.start.valueOf()) {
                    // cancel highlighting
                    prev = null;
                }
            }

            if(prev == null || prevDropLocation.start.valueOf() < prev.start.valueOf() ||
                prevDropLocation.start.valueOf() + length > prev.start.valueOf() + 86400000) {
                $('#plan-science').modal({show: true});
                $(this).addClass('invalid');
                return true;
            }
        }

        $(this).removeClass('invalid');

    }

    body.on('hide.bs.modal', '#plan-science', function () {
       $(this).remove();
    });

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

        var origExternalDrag = $.fullCalendar.Grid.prototype.listenToExternalDrag,
            prevDragged,
            origRenderDrag = $.fullCalendar.View.prototype.renderDrag,
            origDragListener = $.fullCalendar.DragListener.prototype.listenStart;

        $.fullCalendar.DragListener.prototype.listenStart = function (ev) {
            origDragListener.apply(this, [ev]);
            var origCellDone = this.options['cellDone'];
            this.options['cellDone'] = function () {
                if(origCellDone)
                    origCellDone.apply(this);
                prevDropLocation = null;
            };
        };

        $.fullCalendar.View.prototype.renderDrag = function (dropLocation, seg) {
            prevDropLocation = dropLocation;
            this.destroySelection();
            $('.fc-highlight-skeleton').remove();
            if(typeof seg != 'undefined')
                prevDragged = seg.el;
            var s = new Date(this.start.valueOf());
            s.setHours(0, 0, 0, 0);
            s = s.getTime() + 86400000;
            if(!dropLocation.allDay && prevDragged.is('.fc-event.event-type-f')) {
                for(var i = 0; i < 7; i++)
                {
                    // find a study session already in the space
                    var skip = false;
                    for(var j = 0; j < shortlist.length; j++) {
                        if(shortlist[j].className.indexOf('event-type-f') > -1 &&
                            shortlist[j].start.valueOf() >= s + 86400000 * i &&
                            shortlist[j].start.valueOf() <= s + 86400000 * (i + 1)) {
                            // cancel highlighting
                            skip = true;
                        }
                    }
                    if(!skip) {
                        this.renderSelection(
                            this.calendar.ensureVisibleEventRange({
                                start: moment(new Date(s + 86400000 * i)),
                                end: moment(new Date(s + 86400000 * (i + 1)))}) // needs to be a proper range
                        );
                    }
                }
            }
            else if(!dropLocation.allDay && prevDragged.is('.fc-event.event-type-p')) {
                // find the next occurring event of the same class, must come before that.
                for(var i = 0; i < shortlist.length; i++) {
                    if(shortlist[i].className.indexOf('event-type-c') > -1 &&
                        shortlist[i].start.valueOf() > s &&
                        shortlist[i].start.valueOf() < s + 604800000) {
                        // find a study session already in the space
                        var skip = false;
                        for(var j = 0; j < shortlist.length; j++) {
                            if(shortlist[j].className.indexOf('event-type-p') > -1 &&
                                shortlist[j].start.valueOf() <= shortlist[i].start.valueOf() &&
                                shortlist[j].start.valueOf() >= shortlist[i].start.valueOf() - 86400000) {
                                // cancel highlighting
                                skip = true;
                            }
                        }
                        if(!skip) {
                            this.renderSelection(
                                this.calendar.ensureVisibleEventRange({
                                    start: moment(new Date(shortlist[i].start.valueOf() - 86400000)),
                                    end: moment(new Date(shortlist[i].start.valueOf()))}) // needs to be a proper range
                            );
                        }
                    }
                }

            }
            else if(!dropLocation.allDay && prevDragged.is('.fc-event.event-type-sr')) {
                // find the prev occurring event of the same class, must come before that.
                for(var i = 0; i < shortlist.length; i++) {
                    if(shortlist[i].className.indexOf('event-type-c') > -1 &&
                        shortlist[i].start.valueOf() > s &&
                        shortlist[i].start.valueOf() < s + 604800000) {
                        // find a study session already in the space
                        var skip = false;
                        for(var j = 0; j < shortlist.length; j++) {
                            if(shortlist[j].className.indexOf('event-type-sr') > -1 &&
                                shortlist[j].start.valueOf() <= shortlist[i].start.valueOf() + 86400000 &&
                                shortlist[j].start.valueOf() >= shortlist[i].start.valueOf()) {
                                // cancel highlighting
                                skip = true;
                            }
                        }
                        if(!skip) {
                            this.renderSelection(
                                this.calendar.ensureVisibleEventRange({
                                    start: moment(new Date(shortlist[i].end.valueOf())),
                                    end: moment(new Date(shortlist[i].start.valueOf() + 86400000))}) // needs to be a proper range
                            );
                        }
                    }
                }

            }
            else {
                origRenderDrag.apply(this, [dropLocation, seg]);
            }
        };

        $.fullCalendar.Grid.prototype.listenToExternalDrag = function (el, ev, ui) {
            var classI = (/class([0-9]+)(\s|$)/ig).exec(el.attr('class'));
            var type = (/event-type-([a-z]*?)(\s|$)/ig).exec(el.attr('class'));
            if(type == null) {
                return false;
            }
            if(classI != null) {
                shortlist = calendar.fullCalendar('clientEvents').filter(function (e) {
                    return e.className.indexOf('class' + classI[1]) > -1 && e != ev;
                });
            }
            else {
                shortlist = calendar.fullCalendar('clientEvents').filter(function (e) {
                    return e.className.indexOf('event-type-' + type[1]) > -1 && e != ev;
                });
            }
            prevDragged = el;
            this.renderDrag = function (dropLocation, seg) {
                $.fullCalendar.View.prototype.renderDrag.apply(this.view, [dropLocation, seg]);
            };
            this.destroyDrag = function () {
                $.fullCalendar.View.prototype.destroyDrag.apply(this.view);
                $('.fc-highlight-skeleton').remove();
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

        var isOverlapping;
        calendar = $('#calendar').fullCalendar({
            //minTime: '06:00:00',
            //maxTime: '26:00:00',
            allDayDefault: false,
            views: {
                day: {
                    columnFormat: 'ddd M/D'
                },
                week: {
                    columnFormat: 'ddd M/D'
                },
                month: {
                    columnFormat: 'dddd'
                }
            },
            defaultDate: plans.is('.setup-mode') ? (new Date(Math.min.apply(null, window.planEvents.filter(function (e) {return e.className.indexOf('event-type-c') > -1}).map(function (e) {
                return e.start.valueOf();})) + 604800000)) : null,
            height: 600,
            editable: true,
            draggable: true,
            droppable: true, // this allows things to be dropped onto the calendar
            //aspectRatio: 1.9,
            timezone: 'local',
            businessHours: true,
            snapDuration: '00:15:00',
            firstHour: new Date().getHours(),
            viewRender: function( view, element )
            {
                var plan = $('#plan');
                plan.removeClass('agendaDay agendaWeek month').addClass(view.name);
                if(view.name != 'agendaDay') {
                    plan.find('.mini-checkin').hide();
                }
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
            eventAfterAllRender: function () {
                var calendar = $('#calendar'),
                    plan = $('#plan'),
                    view = calendar.fullCalendar('getView');
                if(view.name == 'agendaWeek' && plans.is('.setup-mode,add-events')) {
                    view.dayGrid.colHeadFormat = 'dddd';
                    calendar.find('.fc-widget-header thead > tr').replaceWith(view.dayGrid.rowHtml('head'));
                }
                else {
                    view.dayGrid.colHeadFormat = 'ddd M/D';
                }
                setTimeout(function () {
                    if(view.name == 'agendaDay' && plan.find('.session-strategy:visible').length > 0 &&
                        plan.find('.fc-event.event-selected:visible').length == 0) {
                        var closest = null;
                        calendar.find('.fc-event:not(.event-type-d)').each(function () {
                            if(closest == null || moment().valueOf() - $(this).data('event').start.valueOf() < moment() - closest.data('event').start.valueOf()) {
                                closest = $(this);
                            }
                        });
                        if(closest != null) {
                            closest.trigger('click');
                        }
                    }
                }, 30);
                if(plan.is('.setup-mode') || plan.is('.add-events')) {
                    calendar.find('h2').text('Your typical week')
                }
                if(!plan.is('.setup-mode,.empty-schedule')) {
                    if(calendar.find('a[href*="/plan/pdf"]').length == 0) {
                        if(body.find('.panel-pane[id^="uid-"]').length > 0) {
                            var uid = (/uid-([0-9]+)/i).exec(body.find('.panel-pane[id^="uid-"]').attr('id'))[1];
                            $('<a href="/plan/pdf/' + uid + '" target="_blank" title="PDF/Print">&nbsp;</a>').appendTo(calendar.find('.fc-right'));
                        }
                        else {
                            $('<a href="/plan/pdf" target="_blank" title="PDF/Print">&nbsp;</a>').appendTo(calendar.find('.fc-right'));
                        }
                    }
                }
                else {
                    calendar.find('a[href*="/plan/pdf"]').remove();
                }
            },
            eventRender: function (event, element) {
                element.data('event', event);
                var view = $('#calendar').fullCalendar('getView');
                if (view.name == 'month' && !event.allDay) return false;
                element.addClass('event-id-' + (typeof event.eventId == 'undefined' ? '' : event.eventId));
                element.find('.fc-title').html(event.title);
                // skip double click event for deadlines
                if(event.className.indexOf('event-type-d') > -1)
                    return true;
                element.bind('dblclick', function () {
                    clearTimeout(clickTimeout);
                    setTimeout(function () {
                        clearTimeout(clickTimeout);
                        var dialog = $('#edit-event');
                        dialog.data('event', event);
                        var title = event.title.replace(/<h4>[^<]*<\/h4>/, '');
                        var close = dialog.find('.modal-header .close').detach();
                        if(event.className.indexOf('event-type-sr') > -1) {
                            dialog.find('.modal-header').text(title + ': Study session');
                        }
                        else if(event.className.indexOf('event-type-p') > -1) {
                            dialog.find('.modal-header').text(title + ': Pre-work');
                        }
                        else if(event.className.indexOf('event-type-d') > -1) {
                            dialog.find('.modal-header').text(title + ': Deadline');
                        }
                        else if(event.className.indexOf('event-type-c') > -1) {
                            dialog.find('.modal-header').text(title + ': Class');
                        }
                        else {
                            dialog.find('.modal-header').text(title);
                        }
                        close.prependTo(dialog.find('.modal-header'));

                        if(event.className.indexOf('event-type-c') > -1) {
                            dialog.addClass('class-only');
                            dialog.find('.title').addClass('read-only');
                        }
                        else {
                            dialog.removeClass('class-only');
                            dialog.find('.title').removeClass('read-only');
                            dialog.find('.start-time input.is-timeEntry').timeEntry('setTime', event.start.toDate());
                            dialog.find('.end-time input.is-timeEntry').timeEntry('setTime', event.end.toDate());
                            // find the earliest occurrence of this event
                            dialog.find('.start-date input[type="text"]').datepicker('setDate', event.start.toDate());
                            dialog.find('.end-date input[type="text"]').datepicker('setDate', event.end.toDate());
                        }
                        dialog.find('.title input').val(title);
                        var type = (/event-type-([a-z]*)(\s|$)/ig).exec(event.className.join(' '))[1];
                        if(typeof event.alert == 'undefined') {
                            var alert = $('#plan-step-3').find('[name="event-type-' + type + '"][value="0"]:checked, select[name="event-type-' + type + '"]').first().val();
                            dialog.find('.reminder select').val(alert);
                        }
                        else {
                            dialog.find('.reminder select').val(event.alert);
                        }
                        dialog.find('.location input').val(event.location);
                        dialog.modal({show:true});
                    }, 300);
                });
                return true;
            },
            header: {
                left: 'prev,next',
                center: 'title',
                right: 'today agendaDay,agendaWeek,month'
            },
            defaultView: plans.is('.setup-mode') && window.outerWidth > 700 || body.is('.adviser') ? 'agendaWeek' : 'agendaDay',
            selectable: false,
            eventOverlap: function (stillEvent, movingEvent) {
                isOverlapping = stillEvent;
                return true;
            },
            events: function (start, end, timezone, callback) {
                callback(window.planEvents);
            },
            drop: function(date, jsEvent, ui) {

                // TODO: count down to remove with numbers in event
                if(!$(this).is('.invalid'))
                    $(this).remove();
            },
            eventClick: function (event, jsEvent, view) {
                var plan = $('#plan');
                if(body.is('.adviser'))
                    return;
                var classI = (/class([0-9]+)(\s|$)/ig).exec($(this).attr('class'));
                // skip click event for deadlines
                if(plan.is('.setup-mode') || plan.is('.add-events') || event.className.indexOf('event-type-d') > -1)
                    return;
                clearTimeout(clickTimeout);
                clickTimeout = setTimeout(function () {

                    if($('#calendar').fullCalendar('getView').name != 'agendaDay') {
                        calendar.fullCalendar('gotoDate', event.start);
                        calendar.fullCalendar('changeView', 'agendaDay');
                    }
                    plan.addClass('session-selected').scrollintoview(DASHBOARD_MARGINS);
                    // change mini checkin color
                    var notes;
                    if(typeof event.courseId != 'undefined') {
                        plan.find('.mini-checkin').show();
                        plan.find('.mini-checkin a').attr('href', '#class' + classI[1]);
                        plan.find('.mini-checkin a').attr('class', 'checkin ' + classI[0] + ' course-id-' + event.courseId);

                        // show related notes
                        notes = $('#notes').find('.class-row.course-id-' + event.courseId).find('~ .notes .note-row');
                    }
                    else {
                        plan.find('.mini-checkin').hide();
                        notes = [];
                    }

                    if(notes.length == 0) {
                        notes = $('#notes').find('.note-row').sort(function (a,b) {
                            return $(a).attr('data-timestamp') - $(b).attr('data-timestamp');
                        });
                    }
                    plan.find('.session-strategy .note-row').remove();
                    notes.slice(0, Math.min(8, notes.length)).clone().appendTo(plan.find('.session-strategy'))
                        .find('a').attr('href', window.callbackPaths['notes'].replace(/\/tab$/i, ''));

                    // highlight selected event
                    plan.find('.event-selected').removeClass('event-selected');
                    plan.find('#calendar .event-id-' + event.eventId).addClass('event-selected');

                    // set the title
                    plan.find('h2.title').text(event.title
                        .replace(/<h4>C<\/h4>/, 'Class: ')
                        .replace(/<h4>F<\/h4>/, '')
                        .replace(/<h4>D<\/h4>/, 'Deadline: ')
                        .replace(/<h4>P<\/h4>/, 'Pre-work: ')
                        .replace(/<h4>SR<\/h4>/, 'Study session: ')
                        .replace(/<h4>D<\/h4>/, 'Deadlines: '));
                    plan.find('h3.location').html('<strong>Location:</strong> ' + (event.location == null || event.location.trim() == '' ? 'Unspecified' : event.location));
                    plan.find('h3.duration').html('<strong>Duration:</strong> ' + ((event.end.valueOf() - event.start.valueOf()) / 60000) + ' minutes');
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
                        plan.find('[name="strategy-select"]').val('blank');
                    plan.setClock();
                }, 300);
            },
            eventDragStart: function (event) {
                var classI = (/class([0-9]+)(\s|$)/ig).exec($(this).attr('class'));
                var type = (/event-type-([a-z]*?)(\s|$)/ig).exec($(this).attr('class'));
                if(classI != null) {
                    shortlist = calendar.fullCalendar('clientEvents').filter(function (e) {
                        return e.className.indexOf('class' + classI[1]) > -1 && e != event;
                    });
                }
                else if(type != null) {
                    shortlist = calendar.fullCalendar('clientEvents').filter(function (e) {
                        return e.className.indexOf('event-type-' + type[1]) > -1 && e != event;
                    });
                }
                else {
                    shortlist = [];
                }
            },
            eventResizeStart: function (event) {
                var classI = (/class([0-9]+)(\s|$)/ig).exec($(this).attr('class'));
                var type = (/event-type-([a-z]*?)(\s|$)/ig).exec($(this).attr('class'));
                if(classI != null) {
                    shortlist = calendar.fullCalendar('clientEvents').filter(function (e) {
                        return e.className.indexOf('class' + classI[1]) > -1 && e != event;
                    });
                }
                else if(type != null) {
                    shortlist = calendar.fullCalendar('clientEvents').filter(function (e) {
                        return e.className.indexOf('event-type-' + type[1]) > -1 && e != event;
                    });
                }
                else {
                    shortlist = [];
                }
            },
            eventDragStop: function (event, jsEvent, ui, view) {
                $('.fc-highlight-skeleton').remove();
            },
            eventDrop: function (event, delta, revertFunc) {

                if (event.allDay) {
                    revertFunc();
                    return;
                }

                var length = event.end.valueOf() - event.start.valueOf(),
                    date = event.start;
                var view = $('#calendar').fullCalendar('getView');
                var tryPlace = 5;
                isOverlapping = null;
                do {
                    if(isOverlapping != null) {
                        date = moment(isOverlapping.end.valueOf() + 60 * 15 * 1000);
                    }
                    isOverlapping = null;
                    view.calendar.isEventRangeAllowed({start: date, end: moment(date.valueOf() + length)}, event);
                    tryPlace--;
                } while(tryPlace > 0 && isOverlapping != null);
                if(isOverlapping == null) {
                    event.start = date;
                    event.end = moment(event.start.valueOf() + length);
                    calendar.fullCalendar('updateEvent', event);
                }

                prevDropLocation = {start: event.start, end: event.end};
                if(shouldRevert.apply(this)) {
                    revertFunc();
                }

                // setup mode saves events when we are all done with the step
                var plan = $('#plan');
                if(!plan.is('.add-events')) {
                    $('#plan-drag').modal({show:true})
                        .one('click.dragging', '.modal-footer a', function () {
                            loadingAnimation($('#plan-drag').find($(this)));
                            saveEventTimes(event, $(this).is('.btn-primary'), revertFunc);
                        })
                        .one('click.dragging', 'a[href="#close"]', revertFunc);
                }
            },
            eventReceive: function (event) {
                var length = event.end ? (event.end.valueOf() - event.start.valueOf()) : 3600000,
                    date = event.start.clone();
                var view = $('#calendar').fullCalendar('getView');
                var tryPlace = 5;
                isOverlapping = null;
                do {
                    if(isOverlapping != null) {
                        date = moment(isOverlapping.end.valueOf() + 60 * 15 * 1000);
                    }
                    isOverlapping = null;
                    view.calendar.isEventRangeAllowed({start: date, end: moment(date.valueOf() + length)}, event);
                    tryPlace--;
                } while(tryPlace > 0 && isOverlapping != null);
                if(event.start.valueOf() != date.valueOf()) {
                    event.start = moment(date.valueOf());
                    event.end = moment(date.valueOf() + length);
                    calendar.fullCalendar('updateEvent', event);
                }
            },
            eventResize: function(event, delta, revertFunc) {
                if (event.allDay) {
                    revertFunc();
                    return;
                }

                prevDropLocation = {start: event.start, end: event.end};
                if(shouldRevert.apply(this)) {
                    revertFunc();
                }

                // setup mode saves events when we are all done with the step
                var plan = $('#plan');
                if(!plan.is('.add-events')) {
                    $('#plan-drag').modal({show:true})
                        .one('click.dragging', '.modal-footer a', function () {
                            loadingAnimation($('#plan-drag').find($(this)));
                            saveEventTimes(event, $(this).is('.btn-primary'), revertFunc);
                        })
                        .one('click.dragging', 'a[href="#close"]', revertFunc);
                }
                else if(!plan.is('.setup-mode')) {
                    saveEventTimes(event, true, revertFunc);
                }
            }
        });

    }

    function saveEventTimes(event, recurring, revertFunc) {
        $.ajax({
            url: window.callbackPaths['plan_update'],
            type: 'POST',
            dataType: 'text',
            data: {
                eventId: event['eventId'],
                start: event['start'].toJSON(),
                end: event['end'].toJSON(),
                reoccurring: recurring
            },
            success: function (content) {
                $('#plan-drag').find('.squiggle').stop().remove();
                var plan = $('#plan');
                if (!plan.is('.setup-mode') && !plan.is('.connected') && !plan.is('.add-events'))
                    body.addClass('download-plan');
                updatePlan(content)
            },
            error: function () {
                $('#plan-drag').find('.squiggle').stop().remove();
                revertFunc();
            }
        });
    }

    body.on('click', '#plan a[href="#deselect"]', function (evt) {
        evt.preventDefault();
        $('#plan').removeClass('session-selected');
    });

    body.on('hide.bs.modal', '#plan-drag', function () {
        var that = $(this);
        setTimeout(function () {
            that.off('.dragging');
        }, 150);
    });

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

        if (plan.is('.empty-schedule')) {
            $('#plan-empty-schedule').modal({
                backdrop: 'static',
                keyboard: false,
                show: true
            });
        }
        else {
            $('#plan-empty-schedule').modal('hide');
            if(plan.is('.setup-mode')) {
                $('#plan-step-0, #plan-step-1, #plan-step-2, #plan-step-2-2, #plan-step-2-3, #plan-step-3, #plan-step-4, #plan-step-5, #plan-step-6, #plan-step-6-2').first().modal({
                    backdrop: 'static',
                    keyboard: false,
                    show: true
                });
            }
            if(plan.is('.show-connected')) {
                $('#plan-step-6-3').modal({show: true});
            }
        }

    }

    body.on('click', '#plan-step-0 a[href="#plan-step-1"]', function (evt) {
        evt.preventDefault();
        if($(this).parents().is('.invalid'))
            return false;
        $('#plan-step-1').modal({show: true});
    });

    function submitNewEvents(next) {
        var events = calendar.fullCalendar('clientEvents'),
            studyEvents = [], removeEvents = [];

        // check for newly added events
        var eventIds = [];
        for(var i = 0; i < events.length; i++) {
            var type = (/event-type-([a-z]+)(\s|$)/i).exec(events[i].className.join(' '));
            if(type[1] == 'd')
                continue;
            if(typeof events[i].eventId == 'undefined'
                || calendar.find('.event-id-' + events[i].eventId).is(':visible')
                && eventIds.indexOf(events[i].eventId.split('_')[0]) == -1) {
                if(typeof events[i].eventId != 'undefined') {
                    eventIds[eventIds.length] = events[i].eventId.split('_')[0];
                }
                studyEvents[studyEvents.length] = {
                    type: type[1],
                    eventId: events[i].eventId,
                    start: events[i].start.toJSON(),
                    end: events[i].end.toJSON(),
                    alert: events[i].alert,
                    location: events[i].location,
                    title: events[i].title
                        .replace(/<h4>C<\/h4>/, '')
                        .replace(/<h4>F<\/h4>/, '')
                        .replace(/<h4>D<\/h4>/, '')
                        .replace(/<h4>P<\/h4>/, '')
                        .replace(/<h4>SR<\/h4>/, '')
                        .replace(/<h4>D<\/h4>/, ''),
                    courseId: events[i].courseId
                };
                removeEvents[removeEvents.length] = events[i];
            }
        }

        // if we aren't adding any events
        if(studyEvents.length == 0) {
            next();
            return;
        }
        loadingAnimation($('#external-events').removeClass('valid').addClass('invalid').find('a[href="#save-plan"]'));

        $.ajax({
            url: window.callbackPaths['plan_create'],
            type: 'POST',
            dataType: 'text',
            data: {
                events: studyEvents
            },
            success: function (content) {
                $('#external-events').removeClass('invalid').addClass('valid').find('.squiggle').remove();
                calendar.fullCalendar('removeEvents', function (e) {return removeEvents.indexOf(e) > -1;});
                updatePlan(content);
                next();
            },
            error: function () {
                $('#external-events').removeClass('invalid').addClass('valid').find('.squiggle').remove();
            }
        });
    }

    body.on('click', '#plan-step-2 a[href="#add-prework"]', function () {
        $('#plan').addClass('add-events');
        var calendar = $('#calendar');
        var external = $('#external-events');
        external.find('.fc-event').remove();
        external.find('h4').text('Pre-work');
        $('#plan-step-1').find('h4').each(function () {
            var courseId = (/course-id-([0-9]*)(\s|$)/ig).exec($(this).find('span').attr('class'))[1];
            var difficulty = $(this).nextAll('.radio').find(':checked').val();
            // skip none events
            if(difficulty == 'none')
                return true;
            var length = difficulty == 'easy' ? '00:45' : (difficulty == 'tough' ? '01:30' : '01:00');
            var count = parseInt($(this).attr('data-reoccurs')) - calendar.find('.event-type-p.' + $(this).find('span').attr('class').replace(/course-id-([0-9]*)(\s|$)/ig, '')).length;
            if(count <= 0)
                return true;
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
                    stick: true // maintain when user navigates (see docs on the renderEvent method)
                });

                // make the event draggable using jQuery UI
                event.draggable({
                    zIndex: 999,
                    start: function() {
                        $(this).width($('#calendar .fc-event:visible').first().width());
                    },
                    revert: shouldRevert,      // will cause the event to go back to its
                    revertDuration:.15  //  original position after the drag
                });
            }
        });

        body.one('click', '#plan a[href="#save-plan"]', function (evt) {
            evt.preventDefault();
            submitNewEvents(function () {
                $('#plan').removeClass('add-events');
                $('#plan-step-2-2').modal({show: true});
            });
        });
    });

    body.on('click', '#plan-step-2-2 a[href="#add-spaced-repetition"]', function () {
        $('#plan').addClass('add-events');
        var calendar = $('#calendar');
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
            var count = parseInt($(this).attr('data-reoccurs')) - calendar.find('.event-type-sr.' + $(this).find('span').attr('class').replace(/course-id-([0-9]*)(\s|$)/ig, '')).length;
            if(count <= 0)
                return true;
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
                    stick: true // maintain when user navigates (see docs on the renderEvent method)
                });

                // make the event draggable using jQuery UI
                event.draggable({
                    zIndex: 999,
                    start: function() {
                        $(this).width($('#calendar .fc-event:visible').first().width());
                    },
                    revert: shouldRevert,      // will cause the event to go back to its
                    revertDuration: .15  //  original position after the drag
                });
            }
        });

        body.one('click', '#plan a[href="#save-plan"]', function (evt) {
            evt.preventDefault();
            submitNewEvents(function () {
                $('#plan').removeClass('add-events');
                $('#plan-step-2-3').modal({show: true});
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
        for(var i = 0; i < 7; i++) {

            var event = $('<div class="fc-event ui-draggable ui-draggable-handle event-type-f" data-event="1" data-duration="01:00"><div class="fc-title"><h4>F</h4>Free study</div></div>').insertAfter(external.find('> h4'));

            // store data so the calendar knows to render an event upon drop
            event.data('event', {
                title: $.trim(event.find('.fc-title').html()), // use the element's text as the event title
                allDay: false,
                className: 'event-type-f',
                editable: true,
                stick: true // maintain when user navigates (see docs on the renderEvent method)
            });

            // make the event draggable using jQuery UI
            event.draggable({
                zIndex: 999,
                start: function() {
                    $(this).width($('#calendar .fc-event:visible').first().width());
                },
                revert: shouldRevert,      // will cause the event to go back to its
                revertDuration: .15  //  original position after the drag
            });
        }

        $('<p>Drag as many as you like</p>').insertAfter(external.find('> h4'));

        body.one('click', '#plan a[href="#save-plan"]', function (evt) {
            evt.preventDefault();
            submitNewEvents(function () {
                $('#plan').removeClass('add-events');
                $('#plan-step-3').modal({show: true});
            });
        });
    });

    body.on('click', '#plan-step-5 a[href="#make-final"]', function () {
        var plan = $('#plan');
        var external = $('#external-events');
        plan.addClass('add-events').removeClass('setup-mode');
        plan.find('a[href="#save-plan"]').text('Done');
        external.find('.fc-event, p').remove();
        external.find('h4').text('Final adjustments');
        body.one('click', '#plan a[href="#save-plan"]', function (evt) {
            evt.preventDefault();
            submitNewEvents(function () {
                $('#plan').removeClass('add-events');
                $('#plan-step-6').modal({show: true});
            });
        });
    });

    body.on('click', '#plan-step-6 a[href*="/plan/download"]', function () {
        $('#plan-step-6-2').modal({show: true})
    });

    body.on('click', '#plan a[href*="/notes"]', function (evt) {
        evt.preventDefault();
        var plan = $('#plan');
        var event = plan.find('.event-selected').data('event');
        var classI = (/class([0-9]+)(\s|$)/ig).exec(event.className.join(' '));
        var that = $(this);
        var isNew = !that.is('#plan .note-row a');
        body.one('show', '#notes', function () {
            var notes = $('#notes');
            // show hide if it is visible on notes page just like click event
            if(classI != null) {
                notes.find('.mini-checkin a').attr('href', '#class' + classI[1]);
                notes.find('.mini-checkin a').attr('class', 'checkin ' + classI[0] + ' course-id-' + event.courseId);
                notes.addClass('is-checkin');
            }
            else {
                notes.find('.mini-checkin').hide();
                notes.removeClass('is-checkin');
            }

            if(!isNew) {
                var noteId = (/note-id-([a-z0-9\-]*)(\s|$)/ig).exec(that.parents('.note-row').attr('class'))[1];
                setTimeout(function () {
                    notes.find('.note-row.note-id-' + noteId + ' a[href="#view-note"]').trigger('click');
                    notes.setClock();
                }, 150);
            }
            else {
                var strategy = plan.find('[name="strategy-select"]').val();
                setTimeout(function () {
                    notes.find('a[href="#add-note"]').trigger('click');
                    notes.setClock();
                    notes.find('select[name="notebook"]').val(event.courseId);
                    var content = plan.find('.strategy-' + strategy).html();
                    if(strategy == 'spaced' && typeof event.dates != 'undefined') {
                        content = content.replace(/<div class="strategy-review">[\s\S]*?<\/div>/i,
                            '<div class="strategy-review"><label>Review material from:</label>' +
                            event.dates.map(function (d) {return '<en-todo></en-todo>'+d+'<br />';}).join('')) +
                            '</div>';
                    }
                    notes.find('#editor1').html(content);
                    setTimeout(function () {
                        if(typeof CKEDITOR.instances.editor1 != 'undefined') {
                            CKEDITOR.instances['editor1'].setData(content);
                        }
                    }, 150);
                }, 150);
            }
        });
    });

    body.on('click', '#plan .note-row', function (evt) {
        evt.stopPropagation();
        if(!$(evt.target).is('a'))
            $(this).find('a[href*="/notes"]').trigger('click');
    });

    function editEventFunc()
    {
        var dialog = $('#edit-event');
        if(dialog.find('.start-time:visible input').length > 0 && dialog.find('.start-time input').val().trim() == '') {
            dialog.addClass('start-time-required');
        }
        else {
            dialog.removeClass('start-time-required');
        }
        if(dialog.find('.end-time:visible input').length > 0 && dialog.find('.end-time input').val().trim() == '') {
            dialog.addClass('end-time-required');
        }
        else {
            dialog.removeClass('end-time-required');
        }
        if(dialog.find('.start-date:visible input').length > 0 && dialog.find('.start-date input').val().trim() == '') {
            dialog.addClass('start-date-required');
        }
        else {
            dialog.removeClass('start-date-required');
        }
        if(dialog.find('.end-date:visible input').length > 0 && dialog.find('.end-date input').val().trim() == '') {
            dialog.addClass('end-date-required');
        }
        else {
            dialog.removeClass('end-date-required');
        }
        if(dialog.find('.title:not(.read-only) input').length > 0 && dialog.find('.title input').val().trim() == '') {
            dialog.addClass('title-required');
        }
        else {
            dialog.removeClass('title-required');
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

        if(dialog.is('.class-required') || dialog.is('.start-time-required') || dialog.is('.end-time-required')
            || dialog.is('.start-date-required') || dialog.is('.end-date-required') || dialog.is('.title-required')
            || dialog.is('.invalid-date') || dialog.is('.invalid-time'))
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
    body.on('change blur', '#edit-event .start-time input, #edit-event .end-time input, ' +
        '#edit-event .start-date input, #edit-event .end-date input', copyTimes);
    body.on('change keyup', '#edit-event .class-name input, #edit-event .start-time input, #edit-event .title input, ' +
        '#edit-event .end-time input, #edit-event .start-date input, #edit-event .end-date input', editEventFunc);
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

    body.on('show.bs.modal', '#edit-event', editEventFunc);

    function submitEditEvent(reoccurring, changes) {
        changes.reoccurring = reoccurring;
        var plan = $('#plan');
        $.ajax({
            url: window.callbackPaths['plan_update'],
            type: 'POST',
            dataType: 'text',
            data: changes,
            success: function (content) {
                $('#plan-drag, #edit-event').modal('hide').find('.squiggle').remove();
                updatePlan(content);
                if (!plan.is('.setup-mode') && !plan.is('.connected') && !plan.is('.add-events'))
                    body.addClass('download-plan');
            },
            error: function () {
                $('#plan-drag, #edit-event').find('.squiggle').remove();
            }
        });
    }

    body.on('submit', '#edit-event form', function (evt) {
        evt.preventDefault();
        var plan = $('#plan'),
            dialog = $('#edit-event'),
            event = dialog.data('event');
        if(dialog.find('.highlighted-link').is('.invalid')) {
            dialog.addClass('invalid-only');
            return false;
        }
        dialog.find('.highlighted-link').removeClass('valid').addClass('invalid');
        var changes = {
            eventId: event.eventId,
            location: dialog.find('.location input').val(),
            alert: dialog.find('.reminder select').val()
        };
        event.location = changes.location;
        event.alert = changes.alert;
        if(dialog.find('.start-time:visible input').length > 0) {
            var s = dialog.find('.start-date input[type="text"]').datepicker('getDate'),
                st = dialog.find('.start-time input').timeEntry('getTime'),
                e = dialog.find('.end-date input[type="text"]').datepicker('getDate'),
                et = dialog.find('.end-time input').timeEntry('getTime');
            // combine date and times
            var newStart = new Date();
            newStart.setFullYear(s.getFullYear(), s.getMonth(), s.getDate());
            newStart.setHours(st.getHours(), st.getMinutes(), 0);
            changes.start = newStart.toJSON();
            var newEnd = new Date();
            newEnd.setFullYear(e.getFullYear(), e.getMonth(), e.getDate());
            newEnd.setHours(et.getHours(), et.getMinutes(), 0);
            changes.end = newEnd.toJSON();
            event.start = moment(newStart);
            event.end = moment(newEnd);
        }
        if(dialog.find('.title:not(.read-only) input').length > 0) {
            changes.title = dialog.find('.title input').val();
            var type = (/event-type-([a-z]+)(\s|$)/i).exec(event.className.join(' '));
            event.title = '<h4>' + type[1].toUpperCase() + '</h4>' + changes.title;
        }

        if(!plan.is('.add-events')) {
            $('#plan-drag').modal({show: true})
                .one('click.dragging', '.modal-footer a', function () {
                    loadingAnimation($('#plan-drag').find($(this)));
                    submitEditEvent($(this).is('.btn-primary'), changes);
                });
        }
        else {
            dialog.modal('hide');
        }
        $('#calendar').fullCalendar('updateEvent', event);
    });

    // The calendar needs to be in view for sizing information.  This will not initialize when display:none;, so instead
    //   we will activate the calendar only once, when the menu is clicked
    body.on('show', '#plan', function () {
        setupPlan();

        if($(this).is('.setup-mode') && ((navigator.userAgent.toLowerCase().indexOf("iphone") > -1 ||
            navigator.userAgent.toLowerCase().indexOf("ipad") > -1) ||
            navigator.userAgent.toLowerCase().indexOf("android") > -1 ||
            navigator.userAgent.toLowerCase().indexOf("mobile") > -1)) {
            $('#plan-mobile').modal({show:true});
        }
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
                    from = editEvent.find('.start-time input[type="text"]').timeEntry('getTime'),
                    to = editEvent.find('.end-time input[type="text"]').timeEntry('getTime');

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
        loadingAnimation(customization.find('[type="submit"]'));
        var scheduleData = { };
        customization.find('input:checked').each(function () {
            scheduleData[$(this).attr('name')] = $(this).val();
        });

        $.ajax({
            url: window.callbackPaths['profile_update'],
            type: 'POST',
            dataType: 'text',
            data: scheduleData,
            success: function (content) {
                customization.find('.squiggle').stop().remove();
                updatePlan(content);
                if($('#plan').is('.setup-mode'))
                    $('#plan-step-2').modal({show:true});
                else
                    $('#plan-step-3').modal({show:true});
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

    body.on('change', '#plan-step-1 input', step1Func);
    body.on('submit', '#plan-step-1 form', submitStep1);
    body.on('show.bs.modal', '#plan-step-1', step1Func);

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

    function updatePlan(content)
    {
        // update calendar events
        window.planEvents = [];
        // merge scripts
        ssMergeScripts($(content).filter('script:not([src])'));
        for (var j = 0; j < window.planEvents.length; j++) {
            window.planEvents[j].start = moment(window.planEvents[j].start);
            window.planEvents[j].end = moment(window.planEvents[j].end);
        }
        // update plan tab
        if (calendar != null && typeof calendar.fullCalendar != 'undefined')
            calendar.fullCalendar('refetchEvents');

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
        loadingAnimation(customization.find('[type="submit"]'));
        var scheduleData = { };
        customization.find('select').each(function () {
            scheduleData[$(this).attr('name')] = $(this).val();
        });

        $.ajax({
            url: window.callbackPaths['profile_update'],
            type: 'POST',
            dataType: 'text',
            data: scheduleData,
            success: function (content) {
                customization.find('.squiggle').stop().remove();
                updatePlan(content);
                if($('#plan').is('.setup-mode'))
                    $('#plan-step-5').modal({show:true});
                else
                    customization.modal('hide');
            },
            error: function () {
                customization.find('.squiggle').stop().remove();
            }
        });
    }

    body.on('change', '#plan-step-4 select', step4Func);
    body.on('submit', '#plan-step-4 form', submitStep4);
    body.on('show.bs.modal', '#plan-step-4', step4Func);

    function submitStep3(evt) {
        evt.preventDefault();
        var customization = $('#plan-step-3');
        if(customization.find('.highlighted-link').is('.invalid')) {
            customization.addClass('invalid-only');
            return;
        }
        customization.find('.highlighted-link').removeClass('valid').addClass('invalid');
        loadingAnimation(customization.find('[type="submit"]'));

        $.ajax({
            url: window.callbackPaths['profile_update'],
            type: 'POST',
            dataType: 'text',
            data: {
                alerts: {
                    c: customization.find('[name="event-type-c"][value="0"]:checked, select[name="event-type-c"]').first().val(),
                    p: customization.find('[name="event-type-p"][value="0"]:checked, select[name="event-type-p"]').first().val(),
                    sr: customization.find('[name="event-type-sr"][value="0"]:checked, select[name="event-type-sr"]').first().val(),
                    f: customization.find('[name="event-type-f"][value="0"]:checked, select[name="event-type-f"]').first().val(),
                    o: customization.find('[name="event-type-o"][value="0"]:checked, select[name="event-type-o"]').first().val()
                }
            },
            success: function (content) {
                customization.find('.highlighted-link').removeClass('invalid').addClass('valid').find('.squiggle').stop().remove();
                updatePlan(content);
                $('#plan-step-4').modal({show:true});
            },
            error: function () {
                customization.find('.highlighted-link').removeClass('invalid').addClass('valid').find('.squiggle').stop().remove();
            }
        });

    }

    body.on('submit', '#plan-step-3 form', submitStep3);

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
                    // update events
                    var content = $(data);
                    window.planEvents = [];
                    ssMergeScripts(content.filter('script:not([src])'));

                    // update view settings
                    if(content.filter('#plan').is('.demo'))
                        plan.addClass('demo');
                    else
                        plan.removeClass('demo');

                    if(content.filter('#plan').is('.setup-mode'))
                        plan.removeClass('session-selected').addClass('setup-mode');
                    else
                        plan.removeClass('setup-mode');

                    // update dialogs
                    content.filter('#plan-step-1, #plan-step-3, #plan-step-4').each(function () {
                        $('#' + $(this).attr('id')).find('.modal-body').replaceWith($(this).find('.modal-body'));
                    });

                    // show empty dialog if needed
                    if(content.filter('#plan').is('.empty-schedule')) {
                        plan.addClass('empty-schedule');
                    }
                    else {
                        plan.removeClass('empty-schedule');
                    }

                    // reset calendar
                    $('#calendar').fullCalendar('destroy');
                    isInitialized = false;
                    plan.filter(':visible').trigger('show');
                }
            });
        }, 100);
    });

    body.on('change', '.plan-widget .completed input', function () {
        var that = jQuery(this),
            row = that.parents('.session-row'),
            eventId = (/event-id-([0-9TZ_]*)(\s|$)/ig).exec(row.attr('class'))[1];

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

});