
$(document).ready(function () {

    // TODO: remove old unused tabs
    var body = $('body');

    function activateMenu(path, noPush) {
        var that = $(this);
        var i = window.callbackUri.indexOf(path),
            panel = $('#' + window.callbackKeys[i] + '.panel-pane'),
            panelIds = body.find('.panel-pane').map(function () {return $(this).attr('id');}).toArray(),
            item = body.find('.main-menu a[href="' + path + '"]').first();

        // activate the menu
        body.find('.main-menu .active').removeClass('active');
        if(item.length > 0) {
            visits[visits.length] = {path: item[0].pathname, query: item[0].search, hash: item[0].hash, time: (new Date()).toJSON()};
            if(item.parents('ul.collapse').length != 0 &&
                item.parents('ul.collapse')[0] != body.find('.main-menu ul.collapse.in')[0])
                body.find('.main-menu ul.collapse.in').removeClass('in');
            item.addClass('active').parents('ul.collapse').addClass('in').css('height', '');
        }
        else
        {
            // create a mock link to get the browser to parse pathname, query, and hash
            var a = document.createElement('a');
            a.href = path;
            visits[visits.length] = {path: a.pathname, query: a.search, hash: a.hash, time: (new Date()).toJSON()};
        }

        // download the panel
        if(panel.length == 0) {
            if(that.is('a') && that.parents('.panel-pane').length > 0)
                item = item.add(that);
            item.each(function (i, obj) { loadingAnimation($(obj)); });
            if(window.sincluding) {
                setTimeout(function () {
                    activateMenu.apply(that, [path, noPush]);
                }, 1000);
                return;
            }
            window.sincluding = true;
            $.ajax({
                url: window.callbackPaths[window.callbackKeys[i]],
                type: 'GET',
                dataType: 'text',
                success: function (tab) {
                    var content = $(tab),
                        panes = $.merge(content.filter('.panel-pane'), content.find('.panel-pane')),
                        styles = ssMergeStyles(content),
                        scripts = ssMergeScripts(content);
                    content = content.not(styles).not(scripts);

                    // don't ever add panes that are already on the page, this is to help with debugging, but should never really happen
                    if (panelIds.length > 0)
                        panes = panes.not('#' + panelIds.join(', #'));

                    if (panes.length > 0) {
                        content.filter('[id]').each(function () {
                            var id = $(this).attr('id');
                            if($('#' + id).length > 0)
                                content = content.not('#' + id);
                        });
                        panes.hide().insertBefore(body.find('.footer'));
                        content.not(panes).insertBefore(body.find('.footer'));
                        var newPane = content.filter('#' + window.callbackKeys[i]);
                        if (newPane.length == 0) {
                            newPane = content.filter('.panel-pane').first();
                        }
                        content.filter('[id]').trigger('loaded');
                        item.find('.squiggle').stop().remove();
                        activatePanel(newPane, i, noPush, path);
                        window.sincluding = false;
                    }
                },
                error:function () {
                    window.sincluding = false;
                    item.find('.squiggle').stop().remove();
                }
            });
        }
        // collapse menus and show panel if it is not already visible
        else if(!panel.is(':visible')) {
            activatePanel(panel, i, noPush, path);
        }
    }

    function activatePanel(panel, i, noPush, path)
    {
        body.removeClass('right-menu left-menu').find('#left-panel, #right-panel').removeClass('expanded').addClass('collapsed');
        body.find('.modal:visible').modal('hide');
        body.find('.panel-pane:visible').fadeOut(75).delay(75).trigger('hide');
        panel.delay(75).fadeIn(75);
        setTimeout(function () {
            panel.trigger('show');
        }, 100);
        if(!noPush)
            window.history.pushState(window.callbackKeys[i], "", path);
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
            // record this special case where its not a link, everything else is recorded automatically
            visits[visits.length] = {path: window.location.pathname, query: window.location.search, hash: '#expand', time:(new Date()).toJSON()};
            // cancel navigation is we are uncollapsing instead
            evt.preventDefault();
            body.find('#left-panel, #right-panel').not(parent).removeClass('expanded').addClass('collapsed');
            // re-render visible panels
            body.find('.panel-pane:visible').redraw();
            if(parent.is('#left-panel'))
                body.removeClass('right-menu').addClass('left-menu');
            else
                body.removeClass('left-menu').addClass('right-menu');
            parent.removeClass('collapsed').addClass('expanded');
            return false;
        }
        return true;
    }

    setTimeout(function () {
        // load the already loaded tabs
        body.find('.panel-pane').filter('[id]').trigger('loaded')
            // show the already visible tabs
            .filter(':visible').trigger('show');
    }, 100);

    body.on('click', '#left-panel a[href="#collapse"], #right-panel a[href="#collapse"]', function (evt) {
        evt.preventDefault();
        var parent = $(this).parents('#left-panel, #right-panel');
        body.removeClass('right-menu left-menu');
        parent.removeClass('expanded').addClass('collapsed');
    });

    body.on('click', '.main-menu a:not([href])', function (evt) {
        expandMenu.apply(this, [evt]);
        if($($(this).attr('data-parent')).find($(this).attr('data-target')).is('.in')){
            evt.stopPropagation();
        }
    });


    body.on('click', ':not(#left-panel):not(#right-panel):not(#left-panel *):not(#right-panel *)', function () {
        if(body.is('.left-menu') || body.is('.right-menu')) {
            // collapse menus
            body.removeClass('right-menu left-menu');
            body.find('#left-panel, #right-panel').removeClass('expanded').addClass('collapsed');
        }
    });

    function handleLink(evt) {

        var that = $(this),
            el = that[0],
            path = $(this).attr('href'),
            callback = null;
        if(!expandMenu.apply(this, [evt]))
            return;

        // the path is not a callback so just return normally
        if(typeof window.history == 'undefined' || typeof window.history.pushState == 'undefined' ||
            // check if there is a tab with the selected url
            window.callbackUri.indexOf(path) == -1) {
            visits[visits.length] = {path: el.pathname, query: el.search, hash: el.hash, time:(new Date()).toJSON()};
            body.removeClass('right-menu left-menu').find('#left-panel, #right-panel').removeClass('expanded').addClass('collapsed');
        }
        // if the path clicked is a callback, use callback to load the new tab
        else
        {
            evt.preventDefault();
            if(window.callbackKeys[callback] == '_welcome')
                path = window.callbackUri[window.callbackKeys.indexOf('home')];
            activateMenu.apply(this, [path]);
        }
    }

    // capture all callback links
    body.filter('.dashboard-home').on('click', 'a[href]', handleLink);
    body.filter('.dashboard-home').on('dblclick', 'a[href]', handleLink);
    body.filter('.dashboard-home').on('dragstart', 'a[href]', handleLink);

    // TODO: we no longer need this because our tabs are always first?
    //if(window.callbackUri.indexOf(window.location.pathname) > -1)
    //    activateMenu(window.location.pathname);

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
    if(typeof $.fn.jPlayer == 'function') {
        var jp = jQuery('#jquery_jplayer');
        jp.jPlayer({
            swfPath: window.callbackPaths['_welcome'] + 'bundles/studysauce/js',
            solution: 'html,flash',
            supplied: 'mp3,m4a,oga',
            preload: 'metadata',
            volume: 0.8,
            muted: false,
            cssSelectorAncestor: '',
            cssSelector: {
                play: '.minplayer-default-play',
                pause: '.minplayer-default-pause'
            },
            ready: function() {
                var index = window.musicIndex++;
                $(this).jPlayer( "setMedia", {
                    mp3: window.musicLinks[index],
                    m4a: window.musicLinks[index].substr(0, window.musicLinks[index].length - 4) + '.mp4',
                    oga: window.musicLinks[index].substr(0, window.musicLinks[index].length - 4) + '.ogg'
                });
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

    $(window).unload(function () {
        if(typeof checkedInBtn != 'undefined' && body.find(checkedInBtn).length == 0 &&
            window.visits.length > 0)
        {
            $.ajax({url: window.callbackPaths['_visit'] + '?close'});
        }
    });

    var visiting = false;
    setInterval(function () {
        if(visiting)
            return;
        if(visits.length > 0) {
            visiting = true;
            $.ajax({
                url: window.callbackPaths['_visit'] + '?sync',
                type: 'GET',
                data: {},
                success: function () {
                    visiting = false;
                },
                error: function () {
                    visiting = false;
                }
            });
        }
    }, 10000);
});
