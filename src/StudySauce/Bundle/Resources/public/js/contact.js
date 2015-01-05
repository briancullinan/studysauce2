$(document).ready(function () {

    // TODO: bring back chat
    var body = $('body');

    body.on('click', '#contact-support a[href="#submit-contact"], #schedule-demo a[href="#submit-contact"]', function (evt) {
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

    body.on('click', '#bill-parents a[href="#submit-contact"]', function (evt) {
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

    body.on('click', '#student-invite a[href="#submit-contact"]', function (evt) {
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