window.sincluding = false;
window.visits = [];
window.players = [];
function onYouTubeIframeAPIReady() {
    var frames = $(this).find('iframe[src*="youtube.com/embed"]');
    var players = [];
    frames.each(function () {
        players[players.length] = new YT.Player($(this).attr('id'), {
            events: {
                'onStateChange': function (e) {
                    _gaq.push(['_trackPageview', location.pathname + location.search  + '#yt' + e.data]);
                    visits[visits.length] = {path: window.location.pathname, query: window.location.search, hash: '#yt' + e.data, time:(new Date()).toJSON()};
                }
            }
        });
    });
    window.players = $.merge(window.players, players);
}

function ssMergeStyles(content)
{
    var styles = $.merge(content.filter('link[type="text/css"]'), content.find('link[type="text/css"]'));

    $(styles).each(function () {
        var url = $(this).attr('href');
        if (typeof url != 'undefined' && $('link[href="' + url + '"]').length == 0)
            $('head').append('<link href="' + url + '" type="text/css" rel="stylesheet" />');
        else {
            var re = (/url\("(.*?)"\)/ig),
                match,
                media = $(this).attr('media');
            while (match = re.exec($(this).html())) {
                if ($('link[href="' + match[1] + '"]').length == 0 &&
                    $('style:contains("' + match[1] + '")').length == 0) {
                    if (typeof media == 'undefined' || media == 'all')
                        $('head').append('<link href="' + match[1] + '" type="text/css" rel="stylesheet" />');
                    else
                        $('head').append('<style media="' + media + '">@import url("' + match[1] + '");');
                }
            }
        }
    });

    return styles;
}

function ssMergeScripts(content)
{
    var scripts = $.merge(content.filter('script[type="text/javascript"]'), content.find('script[type="text/javascript"]'));

    $(scripts).each(function () {
        var url = $(this).attr('src');
        if (typeof url != 'undefined' && $('script[src="' + url + '"]').length == 0) {
            console.log(url.replace(/\?.*/ig, ''));
            $.getScript(url.replace(/\?.*/ig, ''));
        }
        else {
            try
            {
                eval($(this).text());
            }
            catch(e)
            {
                console.log(e);
            }
        }
    });

    return scripts;
}

$(document).ready(function () {
    window.sincluding = true;
    $('.sinclude').each(function () {
        var that = $(this),
            url = that.attr('data-src');
        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'text',
            success: function (data)
            {
                var newStuff = $(data),
                    styles = ssMergeStyles(newStuff),
                    scripts = ssMergeScripts(newStuff);
                newStuff = newStuff.not(styles).not(scripts);
                // do not merge top level items with IDs that already exist
                newStuff.filter('[id]').each(function () {
                    var id = $(this).attr('id');
                    if($('#' + id).length > 0)
                        newStuff = newStuff.not('#' + id);
                });
                that.replaceWith(newStuff);
                setTimeout(function () {newStuff.filter('[id]').trigger('loaded');}, 100);
            },
            error: function () {
            }
        });
    });
});

$.ajaxPrefilter(function (options, originalOptions) {
    // do not send data for POST/PUT/DELETE
    if (originalOptions.type !== 'GET' || options.type !== 'GET') {
        return;
    }

    var data = originalOptions.data;
    if (originalOptions.data !== undefined) {
        if (Object.prototype.toString.call(originalOptions.data) === '[object String]') {
            data = $.deparam(originalOptions.data); // see http://benalman.com/code/projects/jquery-bbq/examples/deparam/
        }
    } else {
        data = {};
    }

    var visits = window.visits;
    window.visits = [];
    options.data = $.param($.extend(data, { __visits: visits }));
});

if(typeof window.jqAjax == 'undefined') {
    window.jqAjax = $.ajax;
    $.ajax = function (settings) {
        var success = settings.success;
        settings.success = function (data, textStatus, jqXHR) {
            if (typeof data == 'string' && data.indexOf('{"redirect":') > -1) {
                try {
                    data = JSON.parse(data.substr(0, 4096)); // no way a redirect would be longer than that
                } catch (ignore) {

                }
            }
            if (data != null && typeof data.redirect != 'undefined') {
                var a = document.createElement('a');
                a.href = data.redirect;
                if (window.location.pathname != a.pathname) {
                    window.location = data.redirect;
                    return;
                }
            }
            if (typeof success != 'undefined')
                success(data, textStatus, jqXHR);
        };
        window.jqAjax(settings);
    };
}

$(document).ajaxError(function(event, jqXHR, ajaxSettings, thrownError) {
    var dialog = $('#error-dialog');
    if(dialog.length > 0)
    {
        var error = '';
        try {
            console.log(thrownError.stack);
            var content = $(jqXHR.responseText);
            if(content.filter('#error'))
                error = content.filter('#error').find('.pane-content').html();
        } catch(ex) {
            error = jqXHR.responseText;
        }
        dialog.find('.modal-body').html(error);
        dialog.modal();
    }
});

$(document).ajaxStop(function () {
    window.sincluding = false;
});

// set some extra utility functions globally

Date.prototype.getWeekNumber = function () {
// Create a copy of this date object
    var target  = new Date(this.valueOf());

    // ISO week date weeks start on monday
    // so correct the day number
    var dayNr   = (this.getDay() + 6) % 7;

    // ISO 8601 states that week 1 is the week
    // with the first thursday of that year.
    // Set the target date to the thursday in the target week
    target.setDate(target.getDate() - dayNr + 3);

    // Store the millisecond value of the target date
    var firstThursday = target.valueOf();

    // Set the target to the first thursday of the year
    // First set the target to january first
    target.setMonth(0, 1);
    // Not a thursday? Correct the date to the next thursday
    if (target.getDay() != 4) {
        target.setMonth(0, 1 + ((4 - target.getDay()) + 7) % 7);
    }

    // The weeknumber is the number of weeks between the
    // first thursday of the year and the thursday in the target week
    return 1 + Math.ceil((firstThursday - target) / 604800000); // 604800000 = 7 * 24 * 3600 * 1000
};

Date.prototype.getFirstDayOfWeek = function () {
    var d = new Date(+this);
    d.setHours(0, 0, 0, 0);
    var day = d.getDay(),
        diff = d.getDate() - day + (day == 0 ? 0:0); // adjust when day is sunday
    return new Date(d.setDate(diff));
};

Date.prototype.addHours = function(h){
    this.setHours(this.getHours()+h);
    return this;
};

$.fn.redraw = function(){
    var that = $(this);
    setTimeout(function () {
        that.each(function(){
            var redraw = this.offsetHeight,
                oldZ = this.zIndex;
            if(typeof this.style != 'undefined') {
                this.style.zIndex = 2;
                this.style.zIndex = oldZ;
                this.style.webkitTransform = 'scale(1)';
            }
        });
    }, 10);
};