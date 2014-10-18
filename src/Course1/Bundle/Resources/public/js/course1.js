$(document).ready(function () {
    var body = $('body');

    body.on('change', '#lesson1-step4 textarea', function () {
        var actions = body.find('#lesson1-step4 .highlighted-link');
        if($(this).val().trim() != '')
            actions.removeClass('invalid').addClass('valid');
        else
            actions.removeClass('valid').addClass('invalid');
    });

    body.on('keyup', '#lesson1-step4 textarea', function () {
        var actions = body.find('#lesson1-step4 .highlighted-link');
        if($(this).val().trim() != '')
            actions.removeClass('invalid').addClass('valid');
        else
            actions.removeClass('valid').addClass('invalid');
    });

    body.on('change', '#lesson1-step2 input[type="radio"]', function () {
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
    });
    body.on('show', '#lesson1-step2', function () {
        body.find('#lesson1-step2 input[type="radio"]').first().trigger('change');
    });
    body.find('#lesson1-step2:visible').trigger('show');

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
});


