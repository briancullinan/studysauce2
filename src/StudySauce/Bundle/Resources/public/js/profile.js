$(document).ready(function () {

    var body = $('body');
    
    function profileFunc() {
        var profile = $('#profile');

        if(profile.find('.grades input:checked').length == 0) {
            profile.addClass('grades-required');
        }
        else {
            profile.removeClass('grades-required');
        }
        if(profile.find('.weekends input:checked').length == 0) {
            profile.addClass('weekends-required');
        }
        else {
            profile.removeClass('weekends-required');
        }
        if(profile.find('input[name="profile-11am"]:checked').length == 0) {
            profile.addClass('profile-11am-required');
        }
        else {
            profile.removeClass('profile-11am-required');
        }
        if(profile.find('input[name="profile-4pm"]:checked').length == 0) {
            profile.addClass('profile-4pm-required');
        }
        else {
            profile.removeClass('profile-4pm-required');
        }
        if(profile.find('input[name="profile-9pm"]:checked').length == 0) {
            profile.addClass('profile-9pm-required');
        }
        else {
            profile.removeClass('profile-9pm-required');
        }
        if(profile.find('input[name="profile-2am"]:checked').length == 0) {
            profile.addClass('profile-2am-required');
        }
        else {
            profile.removeClass('profile-2am-required');
        }

        if(profile.is('.grades-required') || profile.is('.weekends-required') || profile.is('.profile-11am-required') ||
            profile.is('.profile-4pm-required') || profile.is('.profile-9pm-required') || profile.is('.profile-2am-required'))
            profile.find('.highlighted-link').removeClass('valid').addClass('invalid');
        else
            profile.removeClass('invalid-only').find('.highlighted-link').removeClass('invalid').addClass('valid');
    }

    function customizationFunc() {
        var customization = $('#customization'),
            valid = true;

        customization.find('.study-types input, .study-difficulty input').each(function () {
            var inputSet;
            if((inputSet = customization.find('input[name="' + $(this).attr('name') + '"]')).filter(':checked').length == 0) {
                inputSet.parents('label').prev('h4').addClass('type-required');
                valid = false;
            }
            else
                inputSet.parents('label').prev('h4').removeClass('type-required');
        });

        if(valid)
            customization.removeClass('invalid-only').find('.highlighted-link').removeClass('invalid').addClass('valid');
        else
            customization.find('.highlighted-link').removeClass('valid').addClass('invalid');
    }

    body.on('change', '#profile .grades input, #profile .weekends input, #profile .sharpness input', profileFunc);
    body.on('show', '#profile', function () {
        // TODO: update this if payment happens automatically from dashboard
        // show unpaid dialog
        if($(this).is('.demo'))
            $('#profile-upgrade').modal({
                backdrop: 'static',
                keyboard: false,
                show: true
            });
        else {
            $('#profile-upgrade').modal('hide');
            profileFunc();
        }
    });
    body.on('click', 'a[href="#bill-parents"]', function () {
        $('#profile-upgrade').modal('hide');
    });
    body.on('shown.bs.modal', '#bill-parents, #bill-parents-confirm', function () {
        $('#profile-upgrade').modal('hide');
    });
    body.on('hidden.bs.modal', '#bill-parents, #bill-parents-confirm', function () {
        if(!$('#profile').is(':visible'))
            return;
        $('#profile-upgrade').modal({
            backdrop: 'static',
            keyboard: false,
            show: true
        });
    });
    body.on('change', '#customization .study-types input, #customization .study-difficulty input', customizationFunc);
    body.on('show', '#customization', customizationFunc);

    // TODO: make the next unanswered question visible
    function submitProfile(evt)
    {
        evt.preventDefault();
        var profile = $('#profile');
        if(profile.find('.highlighted-link').is('.invalid')) {
            profile.addClass('invalid-only');
            if(profile.is('.grades-required')) {
                profile.find('.grades input').first().focus();
            }
            else if(profile.is('.weekends-required')) {
                profile.find('.weekends input').first().focus();
            }
            else if(profile.is('.profile-11am-required')) {
                profile.find('input[name="profile-11am"]').first().focus();
            }
            else if(profile.is('.profile-4pm-required')) {
                profile.find('input[name="profile-4pm"]').first().focus();
            }
            else if(profile.is('.profile-9pm-required')) {
                profile.find('input[name="profile-9pm"]').first().focus();
            }
            else if(profile.is('.profile-2am-required')) {
                profile.find('input[name="profile-2am"]').first().focus();
            }
            return;
        }
        profile.find('.highlighted-link').removeClass('valid').addClass('invalid');
        loadingAnimation($(this).find('[value="#save-profile"]'));
        var scheduleData = { };
        //university: profile.find('.field-name-field-university input').val(),
        scheduleData['grades'] = profile.find('.grades input:checked').val();
        scheduleData['weekends'] = profile.find('.weekends input:checked').val();
        scheduleData['6-am-11-am'] = profile.find('input[name="profile-11am"]:checked').val();
        scheduleData['11-am-4-pm'] = profile.find('input[name="profile-4pm"]:checked').val();
        scheduleData['4-pm-9-pm'] = profile.find('input[name="profile-9pm"]:checked').val();
        scheduleData['9-pm-2-am'] = profile.find('input[name="profile-2am"]:checked').val();

        $.ajax({
            url: window.callbackPaths['profile_update'],
            type: 'POST',
            dataType: 'text',
            data: scheduleData,
            success: function () {
                profile.find('.squiggle').stop().remove();
                // update plan tab
                body.trigger('scheduled');
            },
            error: function () {
                profile.find('.squiggle').stop().remove();
            }
        });
    }
    body.on('submit', '#profile form', submitProfile);
    function submitCustomization(evt)
    {
        evt.preventDefault();
        var customization = $('#customization');
        if(customization.find('.highlighted-link').is('.invalid')) {
            customization.addClass('invalid-only');
            customization.find('.type-required').first().nextAll('label').find('input').first().focus();
            return;
        }
        customization.find('.highlighted-link').removeClass('valid').addClass('invalid');
        loadingAnimation($(this).find('[value="#save-profile"]'));
        var scheduleData = { };
        customization.find('.study-types input:checked').each(function () {
            scheduleData[$(this).attr('name')] = $(this).val();
        });
        customization.find('.study-difficulty input:checked').each(function () {
            scheduleData[$(this).attr('name')] = $(this).val();
        });

        $.ajax({
            url: window.callbackPaths['profile_update'],
            type: 'POST',
            dataType: 'text',
            data: scheduleData,
            success: function () {
                customization.find('.squiggle').stop().remove();
                // TODO update calendar events
                // TODO: update plan tab
            },
            error: function () {
                customization.find('.squiggle').stop().remove();
            }
        });
    }
    body.on('submit', '#customization form', submitCustomization);
});