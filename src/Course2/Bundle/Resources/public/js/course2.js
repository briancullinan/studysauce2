$(document).ready(function () {
    var body = $('body');

    body.on('show', '.course2.step0', function () {
        body.find('.course2.step0 h3').textfill({widthOnly: true});
        setTimeout(function () {
            body.find('.course2.step0 h3').textfill({widthOnly: true});
        }, 150);
    });

    $(window).resize(function () {
        body.find('.course2.step0 h3').textfill({widthOnly: true});
    });

    body.on('show', '.course2.step1', function () {
        if(!$(this).is('.loaded'))
        {
            $(this).addClass('loaded');
            onYouTubeIframeAPIReady.apply(this);
        }
    });

    body.on('yt1', '.course2.step1', function () {
        var actions = $(this).find('.highlighted-link');
        actions.addClass('played invalid');
        setTimeout(function () {
            actions.removeClass('invalid');
        }, 10000);
    });
    
    body.on('yt0', '.course2.step1', function () {
        $(this).find('.highlighted-link').removeClass('invalid').addClass('played');
    });

    body.on('yt2', '.course2.step1', function () {
        $(this).find('.highlighted-link').removeClass('invalid').addClass('played');
    });

    body.on('hide', '.course2.step1', function () {
        var step = $(this).parents('.course2');
        $(window.players).each(function () {
            if($(this.d).parents(step).length > 0)
                this.pauseVideo();
        });
    });

    function validateInterleaving() {
        var step = body.find('#course2_interleaving-step2'),
            actions = step.find('.highlighted-link'),
            valid = true;
        if(step.find('input[name="quiz-multiple-sessions"]').val().trim() == 0 ||
            step.find('input[name="quiz-other-name"]').val().trim() == 0 ||
            step.find('input[name="quiz-types-courses"]:checked').length == '')
            valid = false;
        if(!valid)
            actions.removeClass('valid').addClass('invalid');
        else
            actions.removeClass('invalid').addClass('valid');
    }

    body.on('change', '#course2_interleaving-step2 input', validateInterleaving);
    body.on('keyup', '#course2_interleaving-step2 input[type="text"]', validateInterleaving);
    body.on('show', '#course2_interleaving-step2', validateInterleaving);
    body.on('click', '#course2_interleaving-step2 a[href="#submit-quiz"]', function (evt) {
        evt.preventDefault();
        var step = body.find('#course2_interleaving-step2'),
            actions = step.find('.highlighted-link');
        if(actions.is('.invalid'))
            return;
        actions.removeClass('valid').addClass('invalid');
        $.ajax({
            url: window.callbackPaths['course2_interleaving_update'],
            type: 'POST',
            dataType: 'text',
            data: {
                multipleSessions: step.find('input[name="quiz-multiple-sessions"]').val().trim(),
                otherName: step.find('input[name="quiz-other-name"]').val().trim(),
                typesCourses: step.find('input[name="quiz-types-courses"]:checked').val()
            },
            success: function (data) {
                var content = $(data);
                step.find('input[name="csrf_token"]').val(content.find('input[name="csrf_token"]').val());

                step.addClass('right');
                actions.removeClass('invalid').addClass('valid');
                // add answers in order
                content.find('h3').each(function (i) {
                    $(this).find('span').appendTo(step.find('h3').eq(i));
                });
                content.find('.results').each(function (i) {
                    $(this).insertAfter(step.find('.questions').eq(i));
                });
                step.scrollintoview(DASHBOARD_MARGINS);
            }
        });
    });

});


