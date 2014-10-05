function onYouTubeIframeAPIReady(playerId) {
    player = new YT.Player('ytplayer', {
        events: {
            'onStateChange': function (e) {
                _gaq.push(['_trackPageview', location.pathname + location.search  + '#yt' + e.data]);
            }
        }
    });
}

$(document).ready(function () {

    Date.prototype.addHours= function(h){
        this.setHours(this.getHours()+h);
        return this;
    };

    function loadingAnimation(that)
    {
        if(typeof that != 'undefined' && that.length > 0 && that.find('.loading').length == 0)
        {
            return loadingAnimation.call($('<small class="loading">&nbsp;</small>').appendTo(that), that);
        }
        else if ($(this).is('.loading'))
        {
            var width = $(this).parent().outerWidth(true);
            return $(this).css('width', 0).css('left', 0)
                .animate({width: width}, 1000, 'swing', function () {
                    var width = $(this).parent().outerWidth(true);
                    $(this).css('width', width).css('left', 0)
                        .animate({left: width, width: 0}, 1000, 'swing', loadingAnimation);
                });
        }
        else if(typeof that != 'undefined')
            return that.find('.loading');
    }

    var body = $('body'),
        jp = $('#jplayer'),
        activateMenu = function (path, noPush) {
            var i = window.callbackUri.indexOf(path);
            var panel = $('#' + window.callbackKeys[i] + '.panel-pane'),
                panelIds = body.find('.panel-pane').map(function () {return $(this).attr('id');}).toArray(),
                item = body.find('.main-menu a[href="' + path + '"]').first();

            // activate the menu
            body.find('.main-menu .active').removeClass('active');
            body.find('.main-menu ul.collapse.in').removeClass('in');
            item.addClass('active').parents('ul.collapse').addClass('in').css('height', '');

            // download the panel
            if(panel.length == 0) {
                loadingAnimation(item);
                $.ajax({
                    url: window.callbackPaths[window.callbackKeys[i]],
                    type: 'GET',
                    dataType: 'text',
                    success: function (tab) {
                        var content = $(tab),
                            panes = $.merge(content.filter('.panel-pane'), content.find('.panel-pane')),
                            styles = $.merge(content.filter('link[type="text/css"]'), content.find('link[type="text/css"]')),
                            scripts = $.merge(content.filter('script[type="text/javascript"]'), content.find('script[type="text/javascript"]'));

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

                        $(scripts).each(function () {
                            var url = $(this).attr('src');
                            if (typeof url != 'undefined' && $('script[src="' + url + '"]').length == 0) {
                                $.getScript(url.replace(/\?.*/ig, ''));
                                console.log(url.replace(/\?.*/ig, ''));
                            }
                        });


                        if (panelIds.length > 0)
                            panes = panes.not('#' + panelIds.join(', #'));
                        if (panes.length > 0) {
                            panes.hide().insertBefore(body.find('.footer'));
                            var newPane = panes.filter('#' + window.callbackKeys[i]);
                            if (newPane.length == 0) {
                                newPane = panes.first();
                            }
                            body.find('#left-panel, #right-panel').removeClass('expanded').addClass('collapsed');
                            setTimeout(function () {
                                item.find('.loading').stop().remove();
                                body.find('.panel-pane:visible').fadeOut(75);
                                newPane.delay(75).fadeIn(75);
                            }, 100);
                            if (!noPush)
                                window.history.pushState(window.callbackKeys[i], "", path);
                        }
                    }
                });
            }
            else if(!panel.is(':visible')) {
                body.find('#left-panel, #right-panel').removeClass('expanded').addClass('collapsed');
                setTimeout(function () {
                    body.find('.panel-pane:visible').fadeOut(75);
                    panel.delay(75).fadeIn(75);
                }, 100);
                if(!noPush)
                    window.history.pushState(window.callbackKeys[i], "", path);
            }
        };

    body.on('click', '#left-panel a[href="#expand"], #right-panel a[href="#expand"]', function (evt) {
        evt.preventDefault();
        var parent = $(this).parents('#left-panel, #right-panel');
        body.find('#left-panel, #right-panel').not(parent).removeClass('expanded').addClass('collapsed');
        parent.removeClass('collapsed').addClass('expanded');
    });

    body.on('click', '#left-panel a[href="#collapse"], #right-panel a[href="#collapse"]', function (evt) {
        evt.preventDefault();
        var parent = $(this).parents('#left-panel, #right-panel');
        parent.removeClass('expanded').addClass('collapsed');
    });

    body.on('click', '.main-menu a:not([href])', function (evt) {
        var parent = $(this).parents('#left-panel, #right-panel');
        if (parent.width() < 150) {
            evt.preventDefault(); // cancel navigation is we are uncollapsing
            body.find('#left-panel, #right-panel').not(parent).removeClass('expanded').addClass('collapsed');
            parent.removeClass('collapsed').addClass('expanded');
        }
    });

    // capture all callback links
    body.on('click', 'a[href]', function (evt) {
        var parent = $(this).parents('#left-panel, #right-panel');
        if(parent.length > 0 && parent.width() < 150) {
            evt.preventDefault(); // cancel navigation is we are uncollapsing
            body.find('#left-panel, #right-panel').not(parent).removeClass('expanded').addClass('collapsed');
            parent.removeClass('collapsed').addClass('expanded');
            return;
        }

        var path = $(this).attr('href');
        // the path is not a callback so just return normally
        if(typeof window.history == 'undefined' || typeof window.history.pushState == 'undefined' ||
                // check if there is a tab with the selected url
            window.callbackUri.indexOf(path) == -1) {
            body.find('#left-panel, #right-panel').removeClass('expanded').addClass('collapsed');
        }
        // if the path clicked is a callback, use callback to load the new tab
        else
        {
            evt.preventDefault();
            activateMenu(path);
        }
    });
    if(window.callbackUri.indexOf(window.location.pathname) > -1)
        activateMenu(window.location.pathname);

    window.onpopstate = function(e){
        if(typeof window.callbackPaths[e.state] != 'undefined') {
            activateMenu(window.callbackUri[window.callbackKeys.indexOf(e.state)], true);
        }
    };

    window.onpushstate = function(e){
        if(typeof window.callbackPaths[e.state] != 'undefined')
        {
            activateMenu(window.callbackUri[window.callbackKeys.indexOf(e.state)], true);
        }
    };

    // -------------- Player --------------- //
    $('.minplayer-default-play').on('click', function () {
        var index = window.musicIndex++;
        jp.jPlayer("setMedia", {
            mp3: window.musicLinks[index],
            m4a: window.musicLinks[index].substr(0, window.musicLinks[index].length - 4) + '.mp4',
            oga: window.musicLinks[index].substr(0, window.musicLinks[index].length - 4) + '.ogg'
        });
    });

    if(typeof $.fn.jPlayer == 'function') {
        window.currentAudio = jp.jPlayer({
            swfPath: '/sites/test.studysauce.com/themes/successinc/js',
            solution: 'html, flash',
            supplied: 'mp3, m4a, oga',
            preload: 'metadata',
            volume: 0.8,
            muted: false,
            cssSelectorAncestor: '.page-dashboard #checkin',
            cssSelector: {
                play: '.minplayer-default-play',
                pause: '.minplayer-default-pause'
            }
        });

        jp.bind($.jPlayer.event.ended, function (event) {
            var index = window.musicIndex++;
            jp.jPlayer("setMedia", {
                mp3: window.musicLinks[index],
                m4a: window.musicLinks[index].substr(0, window.musicLinks[index].length - 4) + '.mp4',
                oga: window.musicLinks[index].substr(0, window.musicLinks[index].length - 4) + '.ogg'
            });
            $(this).jPlayer("play");
        });
    }
    // -------------- END Player --------------- //



});
