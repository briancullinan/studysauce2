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
        var step = $(this);
        if(!step.is('.loaded'))
        {
            step.addClass('loaded');
            onYouTubeIframeAPIReady.apply(this);
        }
        setTimeout(function () {
            for(var i = 0; i < window.players.length; i++) {
                if($(window.players[i].d).parents().is(step)) {
                    window.players[i].playVideo();
                    break;
                }
            }
        }, 1000);
    });

    body.on('show', '.course2.step4', function () {
        // mark lesson completed on the menu
        var step = $(this).attr('id').replace(/-step[0-9]+/ig, ''),
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
        var step = $(this);
        $(window.players).each(function () {
            if($(this.d).parents().is(step))
                this.pauseVideo();
        });
    });

    function validateInterleaving() {
        var step = body.find('#course2_interleaving-step2'),
            actions = step.find('.highlighted-link'),
            valid = true;
        if(step.find('input[name="quiz-multiple-sessions"]').val().trim() == '' ||
            step.find('input[name="quiz-other-name"]').val().trim() == '' ||
            step.find('input[name="quiz-types-courses"]:checked').length == 0)
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

    function validateStudyMetrics() {
        var step = body.find('#course2_study_metrics-step2'),
            actions = step.find('.highlighted-link'),
            valid = true;
        if(step.find('input[name="quiz-track-hours"]:checked').length == 0 ||
            step.find('input[name="quiz-doing-well"]:checked').length == 0 ||
            step.find('input[name="quiz-all-together"]').val().trim() == '')
            valid = false;
        if(!valid)
            actions.removeClass('valid').addClass('invalid');
        else
            actions.removeClass('invalid').addClass('valid');
    }

    body.on('change', '#course2_study_metrics-step2 input', validateStudyMetrics);
    body.on('keyup', '#course2_study_metrics-step2 input[type="text"]', validateStudyMetrics);
    body.on('show', '#course2_study_metrics-step2', validateStudyMetrics);
    body.on('click', '#course2_study_metrics-step2 a[href="#submit-quiz"]', function (evt) {
        evt.preventDefault();
        var step = body.find('#course2_study_metrics-step2'),
            actions = step.find('.highlighted-link');
        if(actions.is('.invalid'))
            return;
        actions.removeClass('valid').addClass('invalid');
        $.ajax({
            url: window.callbackPaths['course2_study_metrics_update'],
            type: 'POST',
            dataType: 'text',
            data: {
                trackHours: step.find('input[name="quiz-track-hours"]:checked').map(function (i, x) {return $(x).val();}).get().join(','),
                doingWell: step.find('input[name="quiz-doing-well"]:checked').val(),
                allTogether: step.find('input[name="quiz-all-together"]').val().trim()
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

    function validateStudyPlan() {
        var step = body.find('#course2_study_plan-step2'),
            actions = step.find('.highlighted-link'),
            valid = true;
        if(step.find('input[name="quiz-multiply"]:checked').length == 0 ||
            step.find('textarea[name="quiz-procrastination"]').val().trim() == '' ||
            step.find('textarea[name="quiz-study-sessions"]').val().trim() == '' ||
            step.find('textarea[name="quiz-stick-plan"]').val().trim() == '')
            valid = false;
        if(!valid)
            actions.removeClass('valid').addClass('invalid');
        else
            actions.removeClass('invalid').addClass('valid');
    }

    body.on('change', '#course2_study_plan-step2 input, #course2_study_plan-step2 textarea', validateStudyPlan);
    body.on('keyup', '#course2_study_plan-step2 textarea', validateStudyPlan);
    body.on('show', '#course2_study_plan-step2', validateStudyPlan);
    body.on('click', '#course2_study_plan-step2 a[href="#submit-quiz"]', function (evt) {
        evt.preventDefault();
        var step = body.find('#course2_study_plan-step2'),
            actions = step.find('.highlighted-link');
        if(actions.is('.invalid'))
            return;
        actions.removeClass('valid').addClass('invalid');
        $.ajax({
            url: window.callbackPaths['course2_study_plan_update'],
            type: 'POST',
            dataType: 'text',
            data: {
                multiply: step.find('input[name="quiz-multiply"]:checked').val(),
                procrastination: step.find('textarea[name="quiz-procrastination"]').val(),
                studySessions: step.find('textarea[name="quiz-study-sessions"]').val(),
                stickPlan: step.find('textarea[name="quiz-stick-plan"]').val()
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

    function validateStudyTests() {
        var step = body.find('#course2_study_tests-step2'),
            actions = step.find('.highlighted-link'),
            valid = true;
        if(step.find('input[name="quiz-types-tests"]:checked').length == 0 ||
            step.find('input[name="quiz-most-important"]').val().trim() == '' ||
            step.find('input[name="quiz-open-tips-1"]').val().trim() == '' ||
            step.find('input[name="quiz-open-tips-2"]').val().trim() == '')
            valid = false;
        if(!valid)
            actions.removeClass('valid').addClass('invalid');
        else
            actions.removeClass('invalid').addClass('valid');
    }

    body.on('change', '#course2_study_tests-step2 input', validateStudyTests);
    body.on('keyup', '#course2_study_tests-step2 input[type="text"]', validateStudyTests);
    body.on('show', '#course2_study_tests-step2', validateStudyTests);
    body.on('click', '#course2_study_tests-step2 a[href="#submit-quiz"]', function (evt) {
        evt.preventDefault();
        var step = body.find('#course2_study_tests-step2'),
            actions = step.find('.highlighted-link');
        if(actions.is('.invalid'))
            return;
        actions.removeClass('valid').addClass('invalid');
        $.ajax({
            url: window.callbackPaths['course2_study_tests_update'],
            type: 'POST',
            dataType: 'text',
            data: {
                typesTests: step.find('input[name="quiz-types-tests"]:checked').val(),
                mostImportant: step.find('input[name="quiz-most-important"]').val(),
                openTips1: step.find('input[name="quiz-open-tips-1"]').val(),
                openTips2: step.find('input[name="quiz-open-tips-2"]').val()
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


    function validateStudyTestsInvestment() {
        var step = body.find('#course2_study_tests-step4'),
            actions = step.find('.highlighted-link'),
            valid = true;
        if(step.find('textarea').val().trim() == '')
            valid = false;
        if(!valid)
            actions.removeClass('valid').addClass('invalid');
        else
            actions.removeClass('invalid').addClass('valid');
    }

    body.on('change', '#course2_study_tests-step4 textarea', validateStudyTestsInvestment);
    body.on('keyup', '#course2_study_tests-step4 textarea', validateStudyTestsInvestment);
    body.on('show', '#course2_study_tests-step4', validateStudyTestsInvestment);
    body.on('click', '#course2_study_tests-step4 .highlighted-link a', function (evt) {
        evt.preventDefault();
        var step = body.find('#course2_study_tests-step4'),
            actions = step.find('.highlighted-link');
        if(actions.is('.invalid'))
            return;
        actions.removeClass('valid').addClass('invalid');
        $.ajax({
            url: window.callbackPaths['course2_study_tests_update'],
            type: 'POST',
            dataType: 'text',
            data: {
                testTypes: step.find('textarea').val()
            },
            success: function (data) {
                var content = $(data);
                step.find('input[name="csrf_token"]').val(content.find('input[name="csrf_token"]').val());

            }
        });
    });

    function validateTestTaking() {
        var step = body.find('#course2_test_taking-step2'),
            actions = step.find('.highlighted-link'),
            valid = true;
        if(step.find('input[name="quiz-idea-cram"]:checked').length == 0 ||
            step.find('input[name="quiz-breathing"]').val().trim() == '' ||
            step.find('input[name="quiz-skimming"]').val().trim() == '')
            valid = false;
        if(!valid)
            actions.removeClass('valid').addClass('invalid');
        else
            actions.removeClass('invalid').addClass('valid');
    }

    body.on('change', '#course2_test_taking-step2 input', validateTestTaking);
    body.on('keyup', '#course2_test_taking-step2 input[type="text"]', validateTestTaking);
    body.on('show', '#course2_test_taking-step2', validateTestTaking);
    body.on('click', '#course2_test_taking-step2 a[href="#submit-quiz"]', function (evt) {
        evt.preventDefault();
        var step = body.find('#course2_test_taking-step2'),
            actions = step.find('.highlighted-link');
        if(actions.is('.invalid'))
            return;
        actions.removeClass('valid').addClass('invalid');
        $.ajax({
            url: window.callbackPaths['course2_test_taking_update'],
            type: 'POST',
            dataType: 'text',
            data: {
                ideaCram: step.find('input[name="quiz-idea-cram"]:checked').val(),
                breathing: step.find('input[name="quiz-breathing"]').val().trim(),
                skimming: step.find('input[name="quiz-skimming"]').val().trim()
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


