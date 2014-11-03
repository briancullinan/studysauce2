$(document).ready(function () {
    var body = $('body');

    function lesson1Func() {
        var actions = body.find('#lesson1-step4 .highlighted-link');
        if(body.find('#lesson1-step4 textarea').val().trim() != '')
            actions.removeClass('invalid').addClass('valid');
        else
            actions.removeClass('valid').addClass('invalid');
    }

    function validateQuiz1() {
        var step = body.find('#lesson1-step2'),
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
        var step = body.find('#lesson2-step2'),
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
    // TODO: remove course 1 tabs when course 2 is selected

    body.find('.course1.step0 h3').textfill({widthOnly: true});
    $(window).resize(function () {
        body.find('.course1.step0 h3').textfill({widthOnly: true});
    });

    body.on('change', '#lesson1-step4 textarea', lesson1Func);
    body.on('keyup', '#lesson1-step4 textarea', lesson1Func);
    body.on('show', '#lesson1-step4', lesson1Func);

    body.on('change', '#lesson1-step2 input[type="radio"]', validateQuiz1);
    body.on('show', '#lesson1-step2', validateQuiz1);
    body.on('click', '#lesson1-step2 a[href="#submit-quiz"]', function (evt) {
        evt.preventDefault();
        var step = body.find('#lesson1-step2'),
            actions = step.find('.highlighted-link');
        if(actions.is('.invalid'))
            return;
        actions.removeClass('valid').addClass('invalid');
        $.ajax({
            url: window.callbackPaths['lesson1_update'],
            type: 'POST',
            dataType: 'json',
            data: {
                education: step.find('input[name="quiz-education"]:checked').val(),
                mindset: step.find('input[name="quiz-mindset"]:checked').val(),
                time: step.find('input[name="quiz-time-management"]:checked').val(),
                devices: step.find('input[name="quiz-devices"]:checked').val(),
                study: step.find('input[name="quiz-study-much"]:checked').val()
            },
            success: function (data) {
                step.find('input[name="csrf_token"]').val(data.csrf_token);

                step.addClass('right');
                actions.removeClass('invalid').addClass('valid');
                // TODO: update study plan
            }
        });
    });

    body.on('change', '#lesson2-step2 input', validateQuiz2);
    body.on('keyup', '#lesson2-step2 input[type="text"]', validateQuiz2);
    body.on('show', '#lesson2-step2', validateQuiz2);
    body.on('click', '#lesson2-step2 a[href="#submit-quiz"]', function (evt) {
        evt.preventDefault();
        var step = body.find('#lesson2-step2'),
            actions = step.find('.highlighted-link');
        if(actions.is('.invalid'))
            return;
        actions.removeClass('valid').addClass('invalid');
        $.ajax({
            url: window.callbackPaths['lesson2_update'],
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
            }
        });
    });

});


