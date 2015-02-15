$(document).ready(function () {

    var body = $('body');
    
    function profileFunc() {
        var profile = $('#profile'),
            valid = true;

        if(profile.find('.grades input:checked').length == 0 ||
            profile.find('.weekends input:checked').length == 0 ||
            profile.find('input[name="profile-11am"]:checked').length == 0 ||
            profile.find('input[name="profile-4pm"]:checked').length == 0 ||
            profile.find('input[name="profile-9pm"]:checked').length == 0 ||
            profile.find('input[name="profile-2am"]:checked').length == 0)
            valid = false;

        if(valid)
            profile.find('.highlighted-link').removeClass('invalid').addClass('valid');
        else
            profile.find('.highlighted-link').removeClass('valid').addClass('invalid');
    }

    function customizationFunc() {
        var customization = $('#customization'),
            valid = true;

        customization.find('.study-types input, .study-difficulty input').each(function () {
            if(customization.find('input[name="' + $(this).attr('name') + '"]:checked').length == 0)
                valid = false;
        });

        if(valid)
            customization.find('.highlighted-link').removeClass('invalid').addClass('valid');
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
    function submitProfile()
    {
        var profile = $('#profile');
        if(profile.find('.highlighted-link').is('.invalid'))
            return;
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
                // TODO update calendar events
                // TODO: update plan tab
            },
            error: function () {
                profile.find('.squiggle').stop().remove();
            }
        });
    }
    body.on('submit', '#profile form', submitProfile);
    function submitCustomization()
    {
        var customization = $('#customization');
        if(customization.find('.highlighted-link').is('.invalid'))
            return;
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