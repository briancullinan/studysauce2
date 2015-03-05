$(document).ready(function () {
    var body = $('body');

    function introductionFunc() {
        var actions = body.find('#course1_introduction-step4 .highlighted-link');
        if(body.find('#course1_introduction-step4 textarea').val().trim() != '')
            actions.removeClass('invalid').addClass('valid');
        else
            actions.removeClass('valid').addClass('invalid');
    }

    function validateQuiz1() {
        var step = body.find('#course1_introduction-step2'),
            actions = step.find('.highlighted-link'),
            valid = true;
        if(step.find('input[name="quiz-education"]:checked').length == 0 ||
            step.find('input[name="quiz-mindset"]:checked').length == 0 ||
            step.find('input[name="quiz-devices"]:checked').length == 0 ||
            step.find('input[name="quiz-study-much"]:checked').length == 0 ||
            step.find('input[name="quiz-time-management"]:checked').length == 0)
            valid = false;
        if(!valid)
            actions.removeClass('valid').addClass('invalid');
        else
            actions.removeClass('invalid').addClass('valid');
    }

    function validateQuiz2() {
        var step = body.find('#course1_setting_goals-step2'),
            actions = step.find('.highlighted-link'),
            valid = true;
        if(step.find('input[name="quiz-goal-performance"]:checked').length == 0 ||
            step.find('input[name="quiz-smart-acronym-S"]').val().trim() == '' ||
            step.find('input[name="quiz-smart-acronym-M"]').val().trim() == '' ||
            step.find('input[name="quiz-smart-acronym-A"]').val().trim() == '' ||
            step.find('input[name="quiz-smart-acronym-R"]').val().trim() == '' ||
            step.find('input[name="quiz-smart-acronym-T"]').val().trim() == '' ||
            step.find('input[name="quiz-motivation-I"]').val().trim() == '' ||
            step.find('input[name="quiz-motivation-E"]').val().trim() == '')
            valid = false;
        if(!valid)
            actions.removeClass('valid').addClass('invalid');
        else
            actions.removeClass('invalid').addClass('valid');
    }

    function validateQuiz3() {
        var step = body.find('#course1_procrastination-step2'),
            actions = step.find('.highlighted-link'),
            valid = true;
        if(step.find('input[name="quiz-memory-A"]').val().trim() == '' ||
            step.find('input[name="quiz-memory-R"]').val().trim() == '' ||
            step.find('input[name="quiz-study-goal"]').val().trim() == '' ||
            step.find('input[name="quiz-stop-procrastinating"]').val().trim() == '' ||
            step.find('input[name="quiz-reduce-procrastination-D"]').val().trim() == '' ||
            step.find('input[name="quiz-reduce-procrastination-P"]').val().trim() == '')
            valid = false;
        if(!valid)
            actions.removeClass('valid').addClass('invalid');
        else
            actions.removeClass('invalid').addClass('valid');
    }

    function validateQuiz4() {
        var step = body.find('#course1_distractions-step2'),
            actions = step.find('.highlighted-link'),
            valid = true;
        if(step.find('input[name="quiz-multitask"]:checked').length == 0 ||
            step.find('input[name="quiz-downside"]:checked').length == 0 ||
            step.find('input[name="quiz-lower-score"]:checked').length == 0 ||
            step.find('input[name="quiz-distraction"]:checked').length == 0)
            valid = false;
        if(!valid)
            actions.removeClass('valid').addClass('invalid');
        else
            actions.removeClass('invalid').addClass('valid');
    }

    body.on('show', '.course1.step0', function () {
        body.find('.course1.step0 h3').textfill({widthOnly: true});
        setTimeout(function () {
            body.find('.course1.step0 h3').textfill({widthOnly: true});
        }, 150);
    });

    $(window).resize(function () {
        body.find('.course1.step0 h3').textfill({widthOnly: true});
    });

    body.on('show', '.course1.step1', function () {
        var step = $(this);
        if(!step.is('.loaded'))
        {
            step.addClass('loaded');
            onYouTubeIframeAPIReady.apply(this);
        }
        var autoPlay = function () {
            for(var i = 0; i < window.players.length; i++) {
                if($(window.players[i]).data('frame').parents().is(step)) {
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

    body.on('show', '.course1.step4', function () {
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

    body.on('yt1', '.course1.step1', function () {
        var actions = $(this).find('.highlighted-link');
        actions.addClass('played invalid');
        setTimeout(function () {
            actions.removeClass('invalid');
        }, 10000);
    });

    body.on('yt0', '.course1.step1', function () {
        $(this).find('.highlighted-link').removeClass('invalid').addClass('played');
    });

    body.on('yt2', '.course1.step1', function () {
        $(this).find('.highlighted-link').removeClass('invalid').addClass('played');
    });

    body.on('hide', '.course1.step1', function () {
        var step = $(this);
        $(window.players).each(function () {
            if($(this.d).parents().is(step))
                this.pauseVideo();
        });
    });

    body.on('change', '#course1_introduction-step4 textarea', introductionFunc);
    body.on('keyup', '#course1_introduction-step4 textarea', introductionFunc);
    body.on('show', '#course1_introduction-step4', introductionFunc);
    body.on('click', '#course1_introduction-step4 .highlighted-link a', function (evt) {
        evt.preventDefault();
        var step = body.find('#course1_introduction-step4'),
            actions = step.find('.highlighted-link');
        if(actions.is('.invalid'))
            return;
        actions.removeClass('valid').addClass('invalid');
        $.ajax({
            url: window.callbackPaths['course1_introduction_update'],
            type: 'POST',
            dataType: 'text',
            data: {
                whyStudy: step.find('textarea').val().trim()
            },
            success: function (data) {
                var content = $(data);
                step.find('input[name="csrf_token"]').val(content.find('input[name="csrf_token"]').val());
            }
        });
    });

    body.on('change', '#course1_introduction-step2 input[type="radio"]', validateQuiz1);
    body.on('show', '#course1_introduction-step2', validateQuiz1);
    body.on('click', '#course1_introduction-step2 a[href="#submit-quiz"]', function (evt) {
        evt.preventDefault();
        var step = body.find('#course1_introduction-step2'),
            actions = step.find('.highlighted-link');
        if(actions.is('.invalid'))
            return;
        actions.removeClass('valid').addClass('invalid');
        $.ajax({
            url: window.callbackPaths['course1_introduction_update'],
            type: 'POST',
            dataType: 'text',
            data: {
                education: step.find('input[name="quiz-education"]:checked').val(),
                mindset: step.find('input[name="quiz-mindset"]:checked').val(),
                time: step.find('input[name="quiz-time-management"]:checked').val(),
                devices: step.find('input[name="quiz-devices"]:checked').val(),
                study: step.find('input[name="quiz-study-much"]:checked').val()
            },
            success: function (data) {
                var content = $(data);
                step.find('input[name="csrf_token"]').val(content.find('input[name="csrf_token"]').val());
                step.addClass('right');
                actions.removeClass('invalid').addClass('valid');
                step.scrollintoview(DASHBOARD_MARGINS);
            }
        });
    });

    body.on('change', '#course1_setting_goals-step2 input', validateQuiz2);
    body.on('keyup', '#course1_setting_goals-step2 input[type="text"]', validateQuiz2);
    body.on('show', '#course1_setting_goals-step2', validateQuiz2);
    body.on('click', '#course1_setting_goals-step2 a[href="#submit-quiz"]', function (evt) {
        evt.preventDefault();
        var step = body.find('#course1_setting_goals-step2'),
            actions = step.find('.highlighted-link');
        if(actions.is('.invalid'))
            return;
        actions.removeClass('valid').addClass('invalid');
        $.ajax({
            url: window.callbackPaths['course1_setting_goals_update'],
            type: 'POST',
            dataType: 'text',
            data: {
                performance: step.find('input[name="quiz-goal-performance"]:checked').val(),
                acronymS: step.find('input[name="quiz-smart-acronym-S"]').val(),
                acronymM: step.find('input[name="quiz-smart-acronym-M"]').val(),
                acronymA: step.find('input[name="quiz-smart-acronym-A"]').val(),
                acronymR: step.find('input[name="quiz-smart-acronym-R"]').val(),
                acronymT: step.find('input[name="quiz-smart-acronym-T"]').val(),
                motivationE: step.find('input[name="quiz-motivation-E"]').val(),
                motivationI: step.find('input[name="quiz-motivation-I"]').val()
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

    body.on('change', '#course1_procrastination-step2 input', validateQuiz3);
    body.on('keyup', '#course1_procrastination-step2 input[type="text"]', validateQuiz3);
    body.on('show', '#course1_procrastination-step2', validateQuiz3);
    body.on('click', '#course1_procrastination-step2 a[href="#submit-quiz"]', function (evt) {
        evt.preventDefault();
        var step = body.find('#course1_procrastination-step2'),
            actions = step.find('.highlighted-link');
        if(actions.is('.invalid'))
            return;
        actions.removeClass('valid').addClass('invalid');
        $.ajax({
            url: window.callbackPaths['course1_procrastination_update'],
            type: 'POST',
            dataType: 'text',
            data: {
                memoryA: step.find('input[name="quiz-memory-A"]').val().trim(),
                memoryR: step.find('input[name="quiz-memory-R"]').val().trim(),
                studyGoal: step.find('input[name="quiz-study-goal"]').val().trim(),
                procrastinating: step.find('input[name="quiz-stop-procrastinating"]').val().trim(),
                procrastinationD: step.find('input[name="quiz-reduce-procrastination-D"]').val().trim(),
                procrastinationP: step.find('input[name="quiz-reduce-procrastination-P"]').val().trim()
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

    body.on('change', '#course1_distractions-step2 input', validateQuiz4);
    body.on('show', '#course1_distractions-step2', validateQuiz4);
    body.on('click', '#course1_distractions-step2 a[href="#submit-quiz"]', function (evt) {
        evt.preventDefault();
        var step = body.find('#course1_distractions-step2'),
            actions = step.find('.highlighted-link');
        if(actions.is('.invalid'))
            return;
        actions.removeClass('valid').addClass('invalid');
        $.ajax({
            url: window.callbackPaths['course1_distractions_update'],
            type: 'POST',
            dataType: 'text',
            data: {
                multitask: step.find('input[name="quiz-multitask"]:checked').val(),
                downside: step.find('input[name="quiz-downside"]:checked').val(),
                lowerScore: step.find('input[name="quiz-lower-score"]:checked').val(),
                distraction: step.find('input[name="quiz-distraction"]:checked').val()
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

    function validateQuiz5() {
        var step = body.find('#course1_environment-step2'),
            actions = step.find('.highlighted-link'),
            valid = true;
        if(step.find('input[name="quiz-environment-bed"]:checked').length == 0 ||
            step.find('input[name="quiz-environment-mozart"]:checked').length == 0 ||
            step.find('input[name="quiz-environment-nature"]:checked').length == 0 ||
            step.find('input[name="quiz-environment-breaks"]:checked').length == 0)
            valid = false;
        if(!valid)
            actions.removeClass('valid').addClass('invalid');
        else
            actions.removeClass('invalid').addClass('valid');
    }

    body.on('change', '#course1_environment-step2 input', validateQuiz5);
    body.on('show', '#course1_environment-step2', validateQuiz5);
    body.on('click', '#course1_environment-step2 a[href="#submit-quiz"]', function (evt) {
        evt.preventDefault();
        var step = body.find('#course1_environment-step2'),
            actions = step.find('.highlighted-link');
        if(actions.is('.invalid'))
            return;
        actions.removeClass('valid').addClass('invalid');
        $.ajax({
            url: window.callbackPaths['course1_environment_update'],
            type: 'POST',
            dataType: 'text',
            data: {
                bed: step.find('input[name="quiz-environment-bed"]:checked').val(),
                mozart: step.find('input[name="quiz-environment-mozart"]:checked').val(),
                nature: step.find('input[name="quiz-environment-nature"]:checked').val(),
                breaks: step.find('input[name="quiz-environment-breaks"]:checked').val()
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


    function validateQuiz6() {
        var step = body.find('#course1_partners-step2'),
            actions = step.find('.highlighted-link'),
            valid = true;
        if(step.find('input[name="quiz-partners-help"]:checked').length == 0 ||
            step.find('input[name="quiz-partners-attribute"]:checked').length == 0 ||
            step.find('input[name="quiz-partners-often"]').val().trim() == '' ||
            step.find('input[name="quiz-partners-usage"]:checked').length == 0)
            valid = false;
        if(!valid)
            actions.removeClass('valid').addClass('invalid');
        else
            actions.removeClass('invalid').addClass('valid');
    }

    body.on('change', '#course1_partners-step2 input', validateQuiz6);
    body.on('keyup', '#course1_partners-step2 input[type="text"]', validateQuiz6);
    body.on('show', '#course1_partners-step2', validateQuiz6);
    body.on('click', '#course1_partners-step2 a[href="#submit-quiz"]', function (evt) {
        evt.preventDefault();
        var step = body.find('#course1_partners-step2'),
            actions = step.find('.highlighted-link');
        if(actions.is('.invalid'))
            return;
        actions.removeClass('valid').addClass('invalid');
        $.ajax({
            url: window.callbackPaths['course1_partners_update'],
            type: 'POST',
            dataType: 'text',
            data: {
                help: step.find('input[name="quiz-partners-help"]:checked').map(function (i, x) {return $(x).val();}).get().join(','),
                attribute: step.find('input[name="quiz-partners-attribute"]:checked').val(),
                often: step.find('input[name="quiz-partners-often"]').val().trim(),
                usage: step.find('input[name="quiz-partners-usage"]:checked').map(function (i, x) {return $(x).val();}).get().join(',')
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


    function validateQuiz7() {
        var step = body.find('#course1_upgrade-step2'),
            actions = step.find('.highlighted-link'),
            valid = true;
        if(step.find('input[name="quiz-enjoyed"]:checked').length == 0)
            valid = false;
        if(!valid)
            actions.removeClass('valid').addClass('invalid');
        else
            actions.removeClass('invalid').addClass('valid');
    }

    body.on('change', '#course1_upgrade-step2 input', validateQuiz7);
    body.on('show', '#course1_upgrade-step2', validateQuiz7);
    body.on('click', '#course1_upgrade-step2 a[href="#submit-quiz"]', function (evt) {
        evt.preventDefault();
        var step = body.find('#course1_upgrade-step2'),
            actions = step.find('.highlighted-link');
        if(actions.is('.invalid'))
            return;
        actions.removeClass('valid').addClass('invalid');
        $.ajax({
            url: window.callbackPaths['course1_upgrade_update'],
            type: 'POST',
            dataType: 'text',
            data: {
                enjoy: step.find('input[name="quiz-enjoyed"]:checked').val()
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


    function saveNetPromoter() {
        var step = body.find('#course1_upgrade-step4');
        $.ajax({
            url: window.callbackPaths['course1_upgrade_update'],
            type: 'POST',
            dataType: 'text',
            data: {
                netPromoter: step.find('input[name="investment-net-promoter"]:checked').val()
            },
            success: function (data) {

            }
        });
    }

    body.on('click', '#course1_upgrade-step4 .highlighted-link a', saveNetPromoter);
    body.on('change', '#course1_upgrade-step4 input', saveNetPromoter);
});


