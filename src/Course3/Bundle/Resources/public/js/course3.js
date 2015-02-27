$(document).ready(function () {
    var body = $('body');

    body.on('show', '.course3.step0', function () {
        body.find('.course3.step0 h3').textfill({widthOnly: true});
        setTimeout(function () {
            body.find('.course3.step0 h3').textfill({widthOnly: true});
        }, 150);
    });

    $(window).resize(function () {
        body.find('.course3.step0 h3').textfill({widthOnly: true});
    });

    body.on('show', '.course3.step1', function () {
        var step = $(this);
        if(!step.is('.loaded'))
        {
            step.addClass('loaded');
            onYouTubeIframeAPIReady.apply(this);
        }
        var autoPlay = function () {
            for(var i = 0; i < window.players.length; i++) {
                if($(window.players[i].d).parents().is(step)) {
                    if(typeof window.players[i].playVideo == 'undefined') {
                        setTimeout(autoPlay, 1000);
                        return;
                    }
                    window.players[i].playVideo();
                    break;
                }
            }
        };
        setTimeout(autoPlay, 1000);
    });

    body.on('show', '.course3.step4', function () {
        // mark lesson completed on the menu
        var step = $(this).attr('id').replace(/-step[0-9]/ig, ''),
            path = window.callbackUri[window.callbackKeys.indexOf(step)];
        $('.main-menu a[href*="' + path + '"]').parent('li').addClass('complete');

        var main = $('.main-menu'),
            completed = Math.round((main.find('#level1 li.complete').length + main.find('#level2 li.complete').length + main.find('#level3 li.complete').length) *
            100 / (main.find('#level1 li').length + main.find('#level2 li').length + main.find('#level3 li').length)),
            widget = $('#home').find('.course-widget');
        widget.find('h3').text(completed + '% of course complete');
        widget.find('.percent-bars').css('height', completed + '%');
        var next = $('.main-menu li:not(.complete)').first();
        widget.find('.highlighted-link').html(next.length == 0 ? '<h4>Complete!</h4>' : ('<a href="' + next.find('a').attr('href') + '" class="more">Next module</a>'));
    });

    body.on('yt1', '.course3.step1', function () {
        var actions = $(this).find('.highlighted-link');
        actions.addClass('played invalid');
        setTimeout(function () {
            actions.removeClass('invalid');
        }, 10000);
    });

    body.on('yt0', '.course3.step1', function () {
        $(this).find('.highlighted-link').removeClass('invalid').addClass('played');
    });

    body.on('yt2', '.course3.step1', function () {
        $(this).find('.highlighted-link').removeClass('invalid').addClass('played');
    });

    body.on('hide', '.course3.step1', function () {
        var step = $(this);
        $(window.players).each(function () {
            if($(this.d).parents().is(step))
                this.pauseVideo();
        });
    });

    function validateStrategies() {
        var step = body.find('#course3_strategies-step2'),
            actions = step.find('.highlighted-link'),
            valid = true;
        if(step.find('input[name="quiz-self-testing"]:checked').length == 0)
            valid = false;
        if(!valid)
            actions.removeClass('valid').addClass('invalid');
        else
            actions.removeClass('invalid').addClass('valid');
    }

    body.on('change', '#course3_strategies-step2 input', validateStrategies);
    body.on('show', '#course3_strategies-step2', validateStrategies);
    body.on('click', '#course3_strategies-step2 a[href="#submit-quiz"]', function (evt) {
        evt.preventDefault();
        var step = body.find('#course3_strategies-step2'),
            actions = step.find('.highlighted-link');
        if(actions.is('.invalid'))
            return;
        actions.removeClass('valid').addClass('invalid');
        $.ajax({
            url: window.callbackPaths['course3_strategies_update'],
            type: 'POST',
            dataType: 'text',
            data: {
                selfTesting: step.find('input[name="quiz-self-testing"]:checked').map(function (i, x) {return $(x).val();}).get().join(',')
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

    function validateGroupStudy() {
        var step = body.find('#course3_group_study-step2'),
            actions = step.find('.highlighted-link'),
            valid = true;
        if(step.find('input[name="quiz-bad-times"]:checked').length == 0 ||
            step.find('input[name="quiz-building"]:checked').length == 0 ||
            step.find('input[name="quiz-group-role"]').val().trim() == '' ||
            step.find('input[name="quiz-group-breaks"]:checked').length == 0)
            valid = false;
        if(!valid)
            actions.removeClass('valid').addClass('invalid');
        else
            actions.removeClass('invalid').addClass('valid');
    }

    body.on('change', '#course3_group_study-step2 input', validateGroupStudy);
    body.on('keyup', '#course3_group_study-step2 input[type="text"]', validateGroupStudy);
    body.on('show', '#course3_group_study-step2', validateGroupStudy);
    body.on('click', '#course3_group_study-step2 a[href="#submit-quiz"]', function (evt) {
        evt.preventDefault();
        var step = body.find('#course3_group_study-step2'),
            actions = step.find('.highlighted-link');
        if(actions.is('.invalid'))
            return;
        actions.removeClass('valid').addClass('invalid');
        $.ajax({
            url: window.callbackPaths['course3_group_study_update'],
            type: 'POST',
            dataType: 'text',
            data: {
                badTimes: step.find('input[name="quiz-bad-times"]:checked').val(),
                building: step.find('input[name="quiz-building"]:checked').val(),
                groupRole: step.find('input[name="quiz-group-role"]').val().trim(),
                groupBreaks: step.find('input[name="quiz-group-breaks"]:checked').val()
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

    function validateGroupStudyInvestment() {
        var step = body.find('#course3_group_study-step4'),
            actions = step.find('.highlighted-link'),
            valid = true;
        if(step.find('textarea').val().trim() == '')
            valid = false;
        if(!valid)
            actions.removeClass('valid').addClass('invalid');
        else
            actions.removeClass('invalid').addClass('valid');
    }

    body.on('change', '#course3_group_study-step4 textarea', validateGroupStudyInvestment);
    body.on('keyup', '#course3_group_study-step4 textarea', validateGroupStudyInvestment);
    body.on('show', '#course3_group_study-step4', validateGroupStudyInvestment);
    body.on('click', '#course3_group_study-step4 .highlighted-link a', function (evt) {
        evt.preventDefault();
        var step = body.find('#course3_group_study-step4'),
            actions = step.find('.highlighted-link');
        if(actions.is('.invalid'))
            return;
        actions.removeClass('valid').addClass('invalid');
        $.ajax({
            url: window.callbackPaths['course3_group_study_update'],
            type: 'POST',
            dataType: 'text',
            data: {
                groupGoals: step.find('textarea').val()
            },
            success: function (data) {
                var content = $(data);
                step.find('input[name="csrf_token"]').val(content.find('input[name="csrf_token"]').val());

            }
        });
    });


    function validateTeaching() {
        var step = body.find('#course3_teaching-step2'),
            actions = step.find('.highlighted-link'),
            valid = true;
        if(step.find('input[name="quiz-new-language"]').val().trim() == '' ||
            step.find('input[name="quiz-memorizing"]:checked').length == 0 ||
            step.find('input[name="quiz-videotaping"]').val().trim() == '')
            valid = false;
        if(!valid)
            actions.removeClass('valid').addClass('invalid');
        else
            actions.removeClass('invalid').addClass('valid');
    }

    body.on('change', '#course3_teaching-step2 input', validateTeaching);
    body.on('keyup', '#course3_teaching-step2 input[type="text"]', validateTeaching);
    body.on('show', '#course3_teaching-step2', validateTeaching);
    body.on('click', '#course3_teaching-step2 a[href="#submit-quiz"]', function (evt) {
        evt.preventDefault();
        var step = body.find('#course3_teaching-step2'),
            actions = step.find('.highlighted-link');
        if(actions.is('.invalid'))
            return;
        actions.removeClass('valid').addClass('invalid');
        $.ajax({
            url: window.callbackPaths['course3_teaching_update'],
            type: 'POST',
            dataType: 'text',
            data: {
                newLanguage: step.find('input[name="quiz-new-language"]').val().trim(),
                memorizing: step.find('input[name="quiz-memorizing"]:checked').val(),
                videotaping: step.find('input[name="quiz-videotaping"]').val().trim()
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



    function validateActiveReading() {
        var step = body.find('#course3_active_reading-step2'),
            actions = step.find('.highlighted-link'),
            valid = true;
        if(step.find('textarea[name="quiz-what-reading"]').val().trim() == '' ||
            step.find('input[name="quiz-highlighting"]:checked').length == 0 ||
            step.find('input[name="quiz-skimming"]:checked').length == 0 ||
            step.find('input[name="quiz-self-explanation"]:checked').length == 0)
            valid = false;
        if(!valid)
            actions.removeClass('valid').addClass('invalid');
        else
            actions.removeClass('invalid').addClass('valid');
    }

    body.on('change', '#course3_active_reading-step2 input', validateActiveReading);
    body.on('keyup', '#course3_active_reading-step2 input[type="text"]', validateActiveReading);
    body.on('show', '#course3_active_reading-step2', validateActiveReading);
    body.on('click', '#course3_active_reading-step2 a[href="#submit-quiz"]', function (evt) {
        evt.preventDefault();
        var step = body.find('#course3_active_reading-step2'),
            actions = step.find('.highlighted-link');
        if(actions.is('.invalid'))
            return;
        actions.removeClass('valid').addClass('invalid');
        $.ajax({
            url: window.callbackPaths['course3_active_reading_update'],
            type: 'POST',
            dataType: 'text',
            data: {
                whatReading: step.find('textarea[name="quiz-what-reading"]').val().trim(),
                highlighting: step.find('input[name="quiz-highlighting"]:checked').val(),
                skimming: step.find('input[name="quiz-skimming"]:checked').val(),
                selfExplanation: step.find('input[name="quiz-self-explanation"]:checked').val()
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



    function validateSpacedRepetition() {
        var step = body.find('#course3_spaced_repetition-step2'),
            actions = step.find('.highlighted-link'),
            valid = true;
        if(step.find('input[name="quiz-space-out"]:checked').length == 0 ||
            step.find('textarea[name="quiz-forgetting"]').val().trim() == '' ||
            step.find('input[name="quiz-revisiting"]:checked').length == 0 ||
            step.find('input[name="quiz-another-name"]:checked').length == 0)
            valid = false;
        if(!valid)
            actions.removeClass('valid').addClass('invalid');
        else
            actions.removeClass('invalid').addClass('valid');
    }

    body.on('change', '#course3_spaced_repetition-step2 input, #course3_spaced_repetition-step2 textarea', validateSpacedRepetition);
    body.on('keyup', '#course3_spaced_repetition-step2 textarea', validateSpacedRepetition);
    body.on('show', '#course3_spaced_repetition-step2', validateSpacedRepetition);
    body.on('click', '#course3_spaced_repetition-step2 a[href="#submit-quiz"]', function (evt) {
        evt.preventDefault();
        var step = body.find('#course3_spaced_repetition-step2'),
            actions = step.find('.highlighted-link');
        if(actions.is('.invalid'))
            return;
        actions.removeClass('valid').addClass('invalid');
        $.ajax({
            url: window.callbackPaths['course3_spaced_repetition_update'],
            type: 'POST',
            dataType: 'text',
            data: {
                spaceOut: step.find('input[name="quiz-space-out"]:checked').val(),
                forgetting: step.find('textarea[name="quiz-forgetting"]').val().trim(),
                revisiting: step.find('input[name="quiz-revisiting"]:checked').val(),
                anotherName: step.find('input[name="quiz-another-name"]:checked').val()
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


    function validateFeedback() {
        var step = body.find('#course3_spaced_repetition-step4'),
            actions = step.find('.highlighted-link'),
            valid = true;
        if(step.find('input:checked').length == 0 ||
            step.find('textarea').val().trim() == '')
            valid = false;
        if(!valid)
            actions.removeClass('valid').addClass('invalid');
        else
            actions.removeClass('invalid').addClass('valid');
    }

    body.on('change', '#course3_spaced_repetition-step4 input, #course3_spaced_repetition-step4 textarea', validateFeedback);
    body.on('keyup', '#course3_spaced_repetition-step4 textarea', validateFeedback);
    body.on('click', '#course3_spaced_repetition-step4 .highlighted-link a', function (evt) {
        var step = body.find('#course3_spaced_repetition-step4');
        evt.preventDefault();
        if(step.find('.highlighted-link').is('invalid')) {
            return;
        }
        step.find('.highlighted-link').addClass('invalid');
        $.ajax({
            url: window.callbackPaths['course3_spaced_repetition_update'],
            type: 'POST',
            dataType: 'text',
            data: {
                netPromoter: step.find('input[name="investment-net-promoter"]:checked').val(),
                feedback: step.find('textarea[name="investment-feedback"]').val().trim()
            },
            success: function (data) {

            }
        });
    });

});


