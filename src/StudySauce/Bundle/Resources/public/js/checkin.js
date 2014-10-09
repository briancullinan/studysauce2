const TIMER_SECONDS = 3600;

$(document).ready(function () {
    var hours = -1,
        minutes = -1,
        sessionStart = null,
        clock = null,
        checkin = $('#checkin');

    function setClock() {
        var seconds = new Date().getTime() / 1000 - sessionStart + 59,
            tmpHours = '' + Math.floor(seconds / 60 / 60),
            tmpMinutes = '' + Math.floor(seconds / 60 % 60);
        if (tmpHours == hours && tmpMinutes == minutes)
            return;
        hours = tmpHours;
        minutes = tmpMinutes;
        checkin.find('.clock').each(function () {
            var that = $(this);
            if (hours.length == 1) {
                that.find('ul:first-of-type').find('li').removeClass('active')
                    .eq(0).addClass('active');
                that.find('ul:nth-of-type(2)').find('li').removeClass('active')
                    .eq(parseInt(hours)).addClass('active');
            }
            else {
                that.find('ul:first-of-type').find('li').removeClass('active')
                    .eq(parseInt(hours.substring(0, 1))).addClass('active');
                that.find('ul:nth-of-type(2)').find('li').removeClass('active')
                    .eq(parseInt(hours.substring(1))).addClass('active');
            }

            if (minutes.length == 1) {
                that.find('ul:nth-of-type(3)').find('li').removeClass('active')
                    .eq(0).addClass('active');
                that.find('ul:nth-of-type(4)').find('li').removeClass('active')
                    .eq(parseInt(minutes)).addClass('active');
            }
            else {
                that.find('ul:nth-of-type(3)').find('li').removeClass('active')
                    .eq(parseInt(minutes.substring(0, 1))).addClass('active');
                that.find('ul:nth-of-type(4)').find('li').removeClass('active')
                    .eq(parseInt(minutes.substring(1))).addClass('active');
            }
        });
    }

    function checkinCallback(pos, cid, checkedIn) {
        var checked = [],
            checklist = $('#checklist'),
            sdsmessages = checkin.find('#sds-messages'),
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
                location: lat + ',' + lng
            },
            success: function (data) {
                var that = checkin.find('#checkin-' + data.cid);

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

    function checkinClick(evt)
    {
        evt.preventDefault();
        var checklist = $('#checklist'),
            sdsmessages = $('#sds-messages'),
            that = $(this),
            id = that.attr('id').substr(8);

        var sessionBegin = function (evt) {
            evt.preventDefault();

            // the default for timer expire is to go to metrics tab
            $('#timer-expire').off('close').on('close', function (evt) {
                evt.preventDefault();
                $('#timer-expire').modal();
                window.location = '#metrics';
            });

            if (sdsmessages.find('.show').length > 0)
            {
                $('#sds-messages').modal();
            }
            else
            {
                checklist.find('.checkboxes input').removeAttr('checked');
                $('#checklist').modal();
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
                checkinCallback(null, id, false);
                that.scrollintoview({padding: {top: 120, bottom: 200, left: 0, right: 0}});
            });
        };

        // if it is in session always display timer expire
        if (that.is('.checked-in'))
        {
            if(clock != null)
                clearInterval(clock);
            clock = null;
            sessionStart = new Date().getTime() / 1000;
            setClock();
            $('#timer-expire').modal();
            checkinCallback(null, id, true);
        }
        else if (checkin.find('.classes a.checked-in').length > 0)
        {
            if(clock != null)
                clearInterval(clock);
            clock = null;
            sessionStart = new Date().getTime() / 1000;
            setClock();

            // switch off other checkin buttons
            var tmpThat = checkin.find('.classes a.checked-in').first();
            checkinCallback(null, tmpThat.attr('id').substr(8), true);

            // show expire message
            $('#timer-expire').off('close').on('close', sessionBegin).modal();
        }
        else
            sessionBegin(evt);
    }

    // perform ajax call when clicked
    checkin.on('click', '.classes a', checkinClick);
    checkin.on('dragstart', '.classes a', checkinClick);

    sessionStart = new Date().getTime() / 1000;
    setClock();
    function startClock() {
        if (clock != null) {
            clearInterval(clock);
            clock = null;
        }
        sessionStart = new Date().getTime() / 1000;
        setClock();
        clock = setInterval(function () {
            setClock();
            if (new Date().getTime() / 1000 - sessionStart >= TIMER_SECONDS - 59) {
                clearInterval(clock);
                clock = null;
                sessionStart = new Date().getTime() / 1000;
                setClock();
                // show expire message
                $('.minplayer-default-pause').trigger('click');
                checkin.find('.classes a.checked-in').first().trigger('click');
            }
        }, 1000);
    }

    function stopClock() {
        if (clock != null) {
            clearInterval(clock);
            clock = null;
        }
        $('.minplayer-default-pause').trigger('click');
        sessionStart = new Date().getTime() / 1000;
        setClock();
    }

    $(window).unload(function () {
        checkin.find('.classes a.checked-in').first().trigger('click');
    });

});