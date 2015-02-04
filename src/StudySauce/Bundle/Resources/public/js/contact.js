$(document).ready(function () {

    // TODO: bring back chat
    var body = $('body');

    body.on('submit', '#contact-support form, #schedule-demo form', function (evt) {
        var contact = $('#contact-support:visible, #schedule-demo:visible');
        evt.preventDefault();
        if(contact.is('.invalid'))
            return;
        contact.removeClass('valid').addClass('invalid');

        jQuery.ajax({
            url: window.callbackPaths['contact_send'],
            type: 'POST',
            dataType: 'json',
            data: {
                name: contact.find('.name input').val(),
                email: contact.find('.email input').val(),
                message: contact.find('.message textarea').val()
            },
            success: function () {
                contact.removeClass('invalid').addClass('valid').modal('hide');
                contact.find('.message textarea').val('');
                contact.modal('hide');
            },
            error: function () {
                contact.removeClass('invalid').addClass('valid');
            }
        });
    });

    body.on('submit', '#bill-parents form', function (evt) {
        var contact = $('#bill-parents');
        evt.preventDefault();
        if(contact.is('.invalid'))
            return;
        contact.removeClass('valid').addClass('invalid');
        var data = {
            first: contact.find('.first-name input').val(),
            last: contact.find('.last-name input').val(),
            email: contact.find('.email input').val()
        };
        if(contact.find('.your-first input').length > 0) {
            data['yourFirst'] = contact.find('.your-first input').val().trim();
            data['yourLast'] = contact.find('.your-last input').val().trim();
            data['yourEmail'] = contact.find('.your-email input').val().trim();
        }
        jQuery.ajax({
            url: window.callbackPaths['contact_parents'],
            type: 'POST',
            dataType: 'json',
            data: data,
            success: function () {
                contact.removeClass('invalid').addClass('valid').modal('hide');
                contact.find('.first-name input, .last-name input, .email input, ' +
                            '.your-first input, .your-last input, .your-email input').val('');
                $('#bill-parents-confirm').modal({show:true});
            },
            error: function () {
                contact.removeClass('invalid').addClass('valid');
            }
        });
    });

    function validateInvite()
    {
        var invite = $(this).parents('#student-invite, #bill-parents');
        var valid = true;
        if(invite.find('.first-name input').val().trim() == '' ||
            invite.find('.last-name input').val().trim() == '' ||
            invite.find('.email input').val().trim() ||
            !(/\b[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,6}\b/i).test(invite.find('.email input').val()))
            valid = false;
        if(invite.find('.your-first input').length > 0) {
            if(invite.find('.your-first input').val().trim() == '' ||
                invite.find('.your-last input').val().trim() == '' ||
                invite.find('.your-email input').val().trim() ||
                !(/\b[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,6}\b/i).test(invite.find('.your-email input').val()))
                valid = false;
        }
        if(valid)
            invite.removeClass('invalid').addClass('valid');
        else
            invite.removeClass('valid').addClass('invalid');
    }

    body.on('keyup', '#student-invite input, #bill-parents input', validateInvite);
    body.on('change', '#student-invite input, #bill-parents input', validateInvite);
    body.on('submit', '#student-invite form', function (evt) {
        var contact = $('#student-invite');
        evt.preventDefault();
        if(contact.is('.invalid'))
            return;
        contact.removeClass('valid').addClass('invalid');
        var data = {
            first: contact.find('.first-name input').val(),
            last: contact.find('.last-name input').val(),
            email: contact.find('.email input').val()
        };
        if(contact.find('.your-first input').length > 0) {
            data['yourFirst'] = contact.find('.your-first input').val().trim();
            data['yourLast'] = contact.find('.your-last input').val().trim();
            data['yourEmail'] = contact.find('.your-email input').val().trim();
        }
        jQuery.ajax({
            url: window.callbackPaths['contact_students'],
            type: 'POST',
            dataType: 'json',
            data: data,
            success: function () {
                contact.removeClass('invalid').addClass('valid').modal('hide');
                contact.find('.first-name input, .last-name input, .email input, ' +
                            '.your-first input, .your-last input, .your-email input').val('');
                $('#student-invite-confirm').modal({show:true});
            },
            error: function () {
                contact.removeClass('invalid').addClass('valid');
            }
        });
    });
});