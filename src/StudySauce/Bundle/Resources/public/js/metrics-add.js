$(document).ready(function () {
    var body = $('body');

    body.on('hide.bs.modal', '#add-study-hours', function () {
        window.location = '/metrics';
    });

    body.on('show.bs.modal', '#add-study-hours', function () {
        // populate class name drop down from current tab
        var tab = $('#metrics:visible, #checkin:visible'),
            courses = {},
            dialog = $(this);
        if(tab.is('#metrics')) {
            tab.find('#legend li').each(function () {
                var that = $(this),
                    courseId = (/course-id-([0-9]+)(\s|$)/ig).exec(that.attr('class'))[1];
                courses[courseId] = that.text().trim();
            });
        }
        else if(tab.is('#checkin')) {
            tab.find('.classes a').each(function () {
                var that = $(this),
                    courseId = (/course-id-([0-9]+)(\s|$)/ig).exec(that.attr('class'))[1];
                courses[courseId] = that.find('span').text();
            });
        }
        dialog.find('.class-name option:not([value=""])').remove();
        for(var c in courses) {
            if(courses.hasOwnProperty(c))
                $('<option value="' + c + '">' + courses[c] + '</option>').appendTo(dialog.find('.class-name select'));
        }
        dialog.find('.date input').datepicker({
            showOtherMonths: true,
            selectOtherMonths: true,
            maxDate: 1,
            autoPopUp:'focus',
            changeMonth: true,
            changeYear: true,
            closeAtTop: false,
            dateFormat: 'mm/dd/yy',
            defaultDate:'0y',
            firstDay:0,
            fromTo:false,
            speed:'immediate',
            yearRange: '-3:+3'
        });
        validateHours();
    });

    function validateHours()
    {
        var dialog = $('#add-study-hours');
        if(dialog.find('.class-name select').val() == '') {
            dialog.addClass('class-required');
        }
        else {
            dialog.removeClass('class-required');
        }
        if(dialog.find('.date input').val() == '') {
            dialog.addClass('date-required');
        }
        else {
            dialog.removeClass('date-required');
        }
        if(dialog.find('.time select').val() == '') {
            dialog.addClass('time-required');
        }
        else {
            dialog.removeClass('time-required');
        }

        if(dialog.is('.class-required') || dialog.is('.date-required') || dialog.is('.time-required')) {
            dialog.find('.highlighted-link').removeClass('valid').addClass('invalid');
        }
        else {
            dialog.removeClass('invalid-only').find('.highlighted-link').removeClass('invalid').addClass('valid');
        }
    }

    body.on('change', '#add-study-hours select, #add-study-hours input', validateHours);
    body.on('keyup', '#add-study-hours input', validateHours);

    body.on('submit', '#add-study-hours form', function (evt) {
        evt.preventDefault();
        var dialog = $('#add-study-hours');
        if(dialog.find('.highlighted-link').is('.invalid')) {
            dialog.addClass('invalid-only');
            if(dialog.is('.class-required')) {
                dialog.find('.class-name select').focus();
            }
            if(dialog.is('.date-required')) {
                dialog.find('.date input').focus();
            }
            if(dialog.is('.time-required')) {
                dialog.find('.time select').focus();
            }
            return;
        }
        dialog.find('.highlighted-link').removeClass('valid').addClass('invalid');
        loadingAnimation(dialog.find('[value="#submit-checkin"]'));
        $.ajax({
            url: window.callbackPaths['checkin_update'],
            type: 'POST',
            dataType: 'text',
            data: {
                courseId: dialog.find('.class-name select').val(),
                checkedIn: 0,
                date: new Date(dialog.find('.date input').val()).toJSON(),
                checklist: '',
                location: ',',
                length: dialog.find('.time select').val()
            },
            success: function () {
                dialog.find('.squiggle').remove();
                body.trigger('checkin');
                dialog.find('.date input, .time select').val('');
                if(dialog.find('.highlighted-link input:checked').length == 0) {
                    dialog.modal('hide');
                }
            },
            error: function () {
                dialog.find('.squiggle').remove();
            }
        })
    });
});