const TIMER_SECONDS = 3600;
const checkinBtn = '#checkin .classes a, #home .checkin-widget a';
const checkedInBtn = '#checkin .classes a.checked-in, #home .checkin-widget a.checked-in';

$(document).ready(function () {
    var hours = -1,
        minutes = -1,
        sessionStart = null,
        sessionCurrent = 0,
        clock = null,
        checkin = $('#checkin'),
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

    body.on('show', '#checkin,#home', function () {
        setTimeout(function () {setClock();}, 200);
    });

    $('#checkin:visible, #home:visible').trigger('show');

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
                $('.minplayer-default-pause').trigger('click');
                body.find(checkedInBtn).first().trigger('click');
            }
        }, 1000);
    }

    function stopClock() {
        if (clock != null) {
            clearInterval(clock);
            clock = null;
        }
        $('.minplayer-default-pause').trigger('click');
        resetClock();
    }

    function checkinCallback(pos, cid, checkedIn) {
        var checked = [],
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
                cid: cid,
                checklist: checked.join(','),
                location: lat + ',' + lng,
                csrf_token: checkin.find('input[name="csrf_token"]').val()
            },
            success: function (data) {
                var that = body.trigger('checkin').find('.checkin.cid' + data.cid);
                checkin.find('input[name="csrf_token"]').val(data.csrf_token);

                // update clock
                if (checkedIn) {
                    stopClock();
                    that.removeClass('checked-in');
                }
                else {
                    that.addClass('checked-in');
                    $.merge(checklist, sdsmessages).modal('hide').find('.modal-footer').removeClass('invalid');
                    startClock();
                }

                // update SDS
                sdsmessages.find('.show').removeClass('show');
                if (typeof data.lastSDS != 'undefined')
                    $('#sds-messages .' + data.lastSDS).addClass('show');
            }
        });
    }

    function sessionBegin(evt, button, cid) {
        var checklist = $('#checklist'),
            sdsmessages = $('#sds-messages');
        evt.preventDefault();

        // the default for timer expire is to go to metrics tab
        $('#timer-expire').off('close').on('close', function (evt) {
            evt.preventDefault();
            $('#timer-expire').modal();
            window.location = '#metrics';
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
            if($(this).parent().is('.invalid'))
                return;
            $(this).parent().addClass('invalid');
            $('.minplayer-default-play').trigger('click');
            //if(typeof navigator.geolocation != 'undefined')
            //{
            //    locationTimeout = setTimeout(callback, 2000);
            //    navigator.geolocation.getCurrentPosition(callback, callback, {maximumAge: 3600000, timeout:1000});
            //}
            //else
            checkinCallback(null, cid, false);
            button.scrollintoview({padding: {top: 120, bottom: 200, left: 0, right: 0}});
        });
    }

    function checkinClick(evt)
    {
        evt.preventDefault();
        var that = $(this),
            cid = (/cid([0-9]+)(\s|$)/ig).exec(that.attr('class'))[1];

        // if it is in session always display timer expire
        if (that.is('.checked-in'))
        {
            if(clock != null)
                clearInterval(clock);
            clock = null;
            resetClock();
            $('#timer-expire').modal();
            checkinCallback(null, cid, true);
        }
        else if (body.find(checkedInBtn).length > 0)
        {
            if(clock != null)
                clearInterval(clock);
            clock = null;
            resetClock();

            // switch off other checkin buttons
            var tmpThat = body.find(checkedInBtn).first();
            checkinCallback(null, (/cid([0-9]+)(\s|$)/ig).exec(tmpThat.attr('class'))[1], true);

            // show expire message
            $('#timer-expire').off('close').on('close', function (evt) {
                sessionBegin(evt, that, cid);
            }).modal();
        }
        else
            sessionBegin(evt, that, cid);
    }

    // perform ajax call when clicked
    body.on('click', checkinBtn, checkinClick);
    body.on('dragstart', checkinBtn, checkinClick);

    resetClock();

    $(window).unload(function () {
        body.find(checkedInBtn).first().trigger('click');
    });

});