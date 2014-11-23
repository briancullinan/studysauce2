const TIMER_SECONDS = 3600;
const checkinBtn = '#checkin .classes a, #home .checkin-widget a, #plan .mini-checkin a';
const checkedInBtn = '#checkin .classes a.checked-in, #home .checkin-widget a.checked-in, #plan .mini-checkin a.checked-in';

$(document).ready(function () {
    var hours = -1,
        minutes = -1,
        sessionStart = null,
        sessionCurrent = 0,
        clock = null,
        body = $('body');

    function setClock() {
        var seconds = sessionCurrent - sessionStart + 59;
        hours = '' + Math.floor(seconds / 60 / 60);
        minutes = '' + Math.floor(seconds / 60 % 60);
        body.find('.clock:visible').each(function () {
            var that = $(this).fitText(1/2);
            if (hours.length == 1) {
                that.find('ul:first-of-type').find('li').removeClass('active').eq(0).addClass('active');
                that.find('ul:nth-of-type(2)').find('li').removeClass('active').eq(parseInt(hours)).addClass('active');
            }
            else {
                that.find('ul:first-of-type').find('li').removeClass('active').eq(parseInt(hours.substring(0, 1))).addClass('active');
                that.find('ul:nth-of-type(2)').find('li').removeClass('active').eq(parseInt(hours.substring(1))).addClass('active');
            }

            if (minutes.length == 1) {
                that.find('ul:nth-of-type(3)').find('li').removeClass('active').eq(0).addClass('active');
                that.find('ul:nth-of-type(4)').find('li').removeClass('active').eq(parseInt(minutes)).addClass('active');
            }
            else {
                that.find('ul:nth-of-type(3)').find('li').removeClass('active').eq(parseInt(minutes.substring(0, 1))).addClass('active');
                that.find('ul:nth-of-type(4)').find('li').removeClass('active').eq(parseInt(minutes.substring(1))).addClass('active');
            }
        });
    }
    // used by mini-checkin
    $.fn.setClock = setClock;

    body.on('scheduled', function () {
        // update classes
        setTimeout(function () {
            $.ajax({
                url: window.callbackPaths['checkin'],
                type: 'GET',
                dataType: 'text',
                success: function (data) {
                    var content = $(data),
                        checkin = $('#checkin');
                    checkin.find('.classes').replaceWith(content.find('.classes'));
                    if(content.filter('#checkin').is('.demo')) {
                        checkin.addClass('demo');
                        $('#checkin-empty').modal();
                    }
                    else
                    {
                        checkin.removeClass('demo');
                        $('#checkin-empty').modal('hide');
                    }
                }
            });
        }, 100);
    });
    body.on('show', '#checkin,#home', function () {
        if($(this).is('#checkin')) {
            if(!$(this).is('.loaded')) {
                $('#jquery_jplayer').jPlayer('option', 'cssSelectorAncestor', '#checkin');
                $(this).addClass('loaded');
            }
            if($(this).is('.demo'))
                $('#checkin-empty').modal({
                    backdrop: 'static',
                    keyboard: false,
                    modalOverflow: true
                });
            else
                $('#checkin-empty').modal('hide');
        }
        setClock();
        setTimeout(function () {
            setClock();
        }, 150);
    });
    body.find('#checkin:visible,#home:visible').trigger('show');

    function resetClock()
    {
        sessionCurrent = sessionStart = new Date().getTime() / 1000;
        setClock();
    }

    function startClock() {
        if (clock != null) {
            clearInterval(clock);
            clock = null;
        }
        resetClock();
        clock = setInterval(function () {
            sessionCurrent = new Date().getTime() / 1000;
            setClock();
            if (sessionCurrent - sessionStart >= TIMER_SECONDS - 59) {
                clearInterval(clock);
                clock = null;
                resetClock();
                // show expire message
                $('#jquery_jplayer').jPlayer("pause");
                body.find(checkedInBtn).first().trigger('click');
            }
        }, 1000);
    }

    function stopClock() {
        if (clock != null) {
            clearInterval(clock);
            clock = null;
        }
        $('#jquery_jplayer').jPlayer("pause");
        resetClock();
    }

    function checkinCallback(pos, courseId, checkedIn) {
        var checkin = $('#checkin'),
            checked = [],
            checklist = $('#checklist'),
            sdsmessages = $('#sds-messages'),
            lat = pos != null && typeof pos.coords != 'undefined' ? pos.coords.latitude : '',
            lng = pos != null && typeof pos.coords != 'undefined' ? pos.coords.longitude : '';
        checklist.find('input:checked').each(function () { checked[checked.length] = $(this).attr('name'); });
        $.ajax({
            url: window.callbackPaths['checkin_update'],
            type: 'POST',
            dataType: 'json',
            data: {
                checkedIn: checkedIn ? 1 : 0,
                date: new Date().toJSON(),
                courseId: courseId,
                checklist: checked.join(','),
                location: lat + ',' + lng,
                csrf_token: checkin.find('input[name="csrf_token"]').val()
            },
            success: function (data) {
                var that = body.find('.checkin.course-id-' + data.courseId);
                checkin.find('input[name="csrf_token"]').val(data.csrf_token);

                // update clock
                if (checkedIn) {
                    stopClock();
                    that.removeClass('checked-in');
                }
                else {
                    that.addClass('checked-in');
                    $.merge(checklist, sdsmessages).modal('hide').removeClass('invalid');
                    startClock();
                }

                // update SDS
                sdsmessages.find('.show').removeClass('show').addClass('hide');
                if (typeof data.lastSDS != 'undefined')
                    sdsmessages.find('.modal-body > div').eq(data.lastSDS).removeClass('hide').addClass('show');
                body.trigger('checkin');
            },
            error: function () {
                $.merge(checklist, sdsmessages).removeClass('invalid');
            }
        });
    }

    function sessionBegin(evt, button, courseId) {
        var checklist = $('#checklist'),
            sdsmessages = $('#sds-messages');
        evt.preventDefault();

        // the default for timer expire is to go to metrics tab
        $('#timer-expire').off('hidden.bs.modal').on('hidden.bs.modal', function (evt) {
            evt.preventDefault();
            activateMenu.apply($('#right-panel').find('a[href*="/metrics"]').first(), [window.callbackUri[window.callbackKeys.indexOf('metrics')]]);
        });

        if (sdsmessages.find('.show').length > 0)
        {
            sdsmessages.modal();
        }
        else
        {
            checklist.find('.checkboxes input').removeAttr('checked');
            checklist.modal();
        }

        $.merge(checklist, sdsmessages).off('click', 'a[href="#study"]').on('click', 'a[href="#study"]', function (evt) {
            evt.preventDefault();
            if($.merge(checklist, sdsmessages).is('.invalid'))
                return;
            $.merge(checklist, sdsmessages).addClass('invalid');
            $('#jquery_jplayer').jPlayer("play");
            //if(typeof navigator.geolocation != 'undefined')
            //{
            //    locationTimeout = setTimeout(callback, 2000);
            //    navigator.geolocation.getCurrentPosition(callback, callback, {maximumAge: 3600000, timeout:1000});
            //}
            //else
            checkinCallback(null, courseId, false);
            button.scrollintoview(DASHBOARD_MARGINS);
        });
    }

    function checkinClick(evt)
    {
        evt.preventDefault();
        var that = $(this),
            courseId = (/course-id-([0-9]+)(\s|$)/ig).exec(that.attr('class'))[1];

        // if it is in session always display timer expire
        if (that.is('.checked-in'))
        {
            if(clock != null)
                clearInterval(clock);
            clock = null;
            resetClock();
            $('#timer-expire').modal();
            checkinCallback(null, courseId, true);
        }
        else if (body.find(checkedInBtn).length > 0)
        {
            if(clock != null)
                clearInterval(clock);
            clock = null;
            resetClock();

            // switch off other checkin buttons
            var tmpThat = body.find(checkedInBtn).first();
            checkinCallback(null, (/course-id-([0-9]+)(\s|$)/ig).exec(tmpThat.attr('class'))[1], true);

            // show expire message
            $('#timer-expire').off('hidden.bs.modal').on('hidden.bs.modal', function (evt) {
                sessionBegin(evt, that, courseId);
            }).modal();
        }
        else
            sessionBegin(evt, that, courseId);
    }

    // perform ajax call when clicked
    body.on('click', checkinBtn, checkinClick);
    body.on('dragstart', checkinBtn, checkinClick);

    resetClock();

    $(window).unload(function () {
        body.find(checkedInBtn).first().trigger('click');
    });

});