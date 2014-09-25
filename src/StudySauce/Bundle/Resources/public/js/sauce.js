$(document).ready(function () {

    var body = $('body'),
        jp = $('#jplayer');

    // look at every link with a hash, store with google analytics and search for dialogs.
    body.on('click', 'a[href^="#"]', function (evt)
    {
        var that = $(this),
            link = that.attr('href');
        if($(link).length > 0 && $(link).is('.dialog'))
        {
            evt.preventDefault();
            $(link).parent('.fixed-centered').show();
            $(link).show(500);
        }
    });

    body.on('click', '.dialog a[href="#close"]', function (evt)
    {
        evt.preventDefault();
        $(this).parents('.dialog').hide(500, function () {
            $(this).parent('.fixed-centered').hide();
        });
    });

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
