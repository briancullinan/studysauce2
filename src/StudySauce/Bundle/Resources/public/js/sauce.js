function onYouTubeIframeAPIReady(playerId) {
    player = new YT.Player('ytplayer', {
        events: {
            'onStateChange': function (e) {
                _gaq.push(['_trackPageview', location.pathname + location.search  + '#yt' + e.data]);
            }
        }
    });
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
            $.getScript(url.replace(/\?.*/ig, ''));
            console.log(url.replace(/\?.*/ig, ''));
        }
        else {
            try
            {
                eval($(this).text());
            }
            catch(e)
            {

            }
        }
    });

    return scripts;
}

$(document).ready(function () {
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
                that.replaceWith(newStuff);
            }
        });
    });
});

$(document).ajaxSuccess(function(event, jqXHR, ajaxOptions, data) {
    if(typeof(data.redirect) != 'undefined')
    {
        window.location = data.redirect;
    }
});

$(document).ajaxError(function(event, jqXHR, ajaxSettings, thrownError) {

});

