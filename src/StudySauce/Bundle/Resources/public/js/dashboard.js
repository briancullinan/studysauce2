
$(document).ready(function () {

    var body = $('body');

    Date.prototype.addHours= function(h){
        this.setHours(this.getHours()+h);
        return this;
    };

    function activateMenu(path, noPush) {
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
            if($(this).is('a') && $(this).parents('.pane-content').length > 0)
                item = item.add($(this));
            item.each(function (i, obj) { loadingAnimation($(obj)); });
            $.ajax({
                url: window.callbackPaths[window.callbackKeys[i]],
                type: 'GET',
                dataType: 'text',
                success: function (tab) {
                    var content = $(tab),
                        panes = $.merge(content.filter('.panel-pane'), content.find('.panel-pane')),
                        styles = ssMergeStyles(content),
                        scripts = ssMergeScripts(content);

                    // don't ever add panes that are already on the page, this is to help with debugging, but should never really happen
                    if (panelIds.length > 0)
                        panes = panes.not('#' + panelIds.join(', #'));

                    if (panes.length > 0) {
                        content.filter('.panel-pane').hide().insertBefore(body.find('.footer'));
                        content.not(panes).not(styles).not(scripts).insertBefore(body.find('.footer'));
                        var newPane = content.filter('#' + window.callbackKeys[i]);
                        if (newPane.length == 0) {
                            newPane = content.filter('.panel-pane').first();
                        }
                        body.removeClass('right-menu left-menu').find('#left-panel, #right-panel').removeClass('expanded').addClass('collapsed');
                        setTimeout(function () {
                            item.find('.squiggle').stop().remove();
                            body.find('.panel-pane:visible').fadeOut(75).delay(75).trigger('hide');
                            newPane.delay(75).fadeIn(75).delay(75).trigger('show');
                        }, 100);
                        if (!noPush)
                            window.history.pushState(window.callbackKeys[i], "", path);
                    }
                }
            });
        }
        else if(!panel.is(':visible')) {
            body.removeClass('right-menu left-menu').find('#left-panel, #right-panel').removeClass('expanded').addClass('collapsed');
            setTimeout(function () {
                body.find('.panel-pane:visible').fadeOut(75).delay(75).trigger('hide');
                panel.delay(75).fadeIn(75).delay(75).trigger('show');
            }, 100);
            if(!noPush)
                window.history.pushState(window.callbackKeys[i], "", path);
        }
    }

    function loadingAnimation(that)
    {
        if(typeof that != 'undefined' && that.length > 0 && that.find('.squiggle').length == 0)
        {
            return loadingAnimation.call($('<small class="squiggle">&nbsp;</small>').appendTo(that), that);
        }
        else if ($(this).is('.squiggle'))
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
            return that.find('.squiggle');
    }

    function expandMenu(evt)
    {
        var parent = $(this).parents('#left-panel, #right-panel');
        if(parent.length > 0 && parent.width() < 150) {
            // cancel navigation is we are uncollapsing instead
            evt.preventDefault();
            body.find('#left-panel, #right-panel').not(parent).removeClass('expanded').addClass('collapsed');
            if(parent.is('#left-panel'))
                body.removeClass('right-menu').addClass('left-menu');
            else
                body.removeClass('left-menu').addClass('right-menu');
            parent.removeClass('collapsed').addClass('expanded');
            return false;
        }
        return true;
    }

    body.on('click', '#left-panel a[href="#expand"], #right-panel a[href="#expand"]', expandMenu);

    body.on('click', '#left-panel a[href="#collapse"], #right-panel a[href="#collapse"]', function (evt) {
        evt.preventDefault();
        var parent = $(this).parents('#left-panel, #right-panel');
        body.removeClass('right-menu left-menu');
        parent.removeClass('expanded').addClass('collapsed');
    });

    body.on('click', '.main-menu a:not([href])', expandMenu);

    function handleLink(evt) {

        var path = $(this).attr('href');
        if(!expandMenu.apply(this, [evt]))
            return;
        // the path is not a callback so just return normally
        if(typeof window.history == 'undefined' || typeof window.history.pushState == 'undefined' ||
            // check if there is a tab with the selected url
            window.callbackUri.indexOf(path) == -1) {
            body.removeClass('right-menu left-menu').find('#left-panel, #right-panel').removeClass('expanded').addClass('collapsed');
        }
        // if the path clicked is a callback, use callback to load the new tab
        else
        {
            evt.preventDefault();
            activateMenu.apply(this, [path]);
        }
    }

    // capture all callback links
    body.filter('.dashboard-home').on('click', 'a[href]', handleLink);
    body.filter('.dashboard-home').on('dblclick', 'a[href]', handleLink);
    body.filter('.dashboard-home').on('dragstart', 'a[href]', handleLink);

    if(window.callbackUri.indexOf(window.location.pathname) > -1)
        activateMenu(window.location.pathname);

    window.onpopstate = function(e){
        if(window.callbackKeys.indexOf(e.state) > -1) {
            activateMenu(window.callbackUri[window.callbackKeys.indexOf(e.state)], true);
        }
        else if (window.callbackUri.indexOf(window.location.pathname) > -1) {
            activateMenu(window.location.pathname, true);
        }
    };

    window.onpushstate = function(e){
        if(window.callbackKeys.indexOf(e.state) > -1) {
            activateMenu(window.callbackUri[window.callbackKeys.indexOf(e.state)], true);
        }
        else if (window.callbackUri.indexOf(window.location.pathname) > -1) {
            activateMenu(window.location.pathname, true);
        }
    };

    // -------------- Player --------------- //
    window.musicIndex = 0;
    var jp = jQuery('#jquery_jplayer');
    body.on('click', '.minplayer-default-play', function () {
        var index = window.musicIndex++;
        jp.jPlayer("setMedia", {
            mp3: window.musicLinks[index],
            m4a: window.musicLinks[index].substr(0, window.musicLinks[index].length - 4) + '.mp4',
            oga: window.musicLinks[index].substr(0, window.musicLinks[index].length - 4) + '.ogg'
        });
        jp.jPlayer("play");
    });

    if(typeof $.fn.jPlayer == 'function') {
        jp.jPlayer({
            swfPath: window.callbackPaths['_welcome'] + 'bundles/studysauce/js',
            solution: 'html,flash',
            supplied: 'mp3,m4a,oga',
            preload: 'metadata',
            volume: 0.8,
            muted: false,
            cssSelectorAncestor: 'body',
            cssSelector: {
                play: '.minplayer-default-play',
                pause: '.minplayer-default-pause'
            }
        });

        jp.bind($.jPlayer.event.ended, function () {
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
