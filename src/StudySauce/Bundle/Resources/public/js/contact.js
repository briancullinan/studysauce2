$(document).ready(function () {

    // TODO: bring back chat
    var body = $('body');

    body.on('click', '#contact-support a[href="#submit-contact"]', function (evt) {
        var contact = $('#contact-support');
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
                contact.modal('hide');
                contact.find('.name input, .email input, .message textarea"]').val('');
            }
        });
    });

    body.on('click', '#bill-parents a[href="#submit-contact"]', function (evt) {
        var contact = $('#bill-parents');
        evt.preventDefault();
        if(contact.is('.invalid'))
            return;
        contact.removeClass('valid').addClass('invalid');

        jQuery.ajax({
            url: window.callbackPaths['contact_parents'],
            type: 'POST',
            dataType: 'json',
            data: {
                firstName: contact.find('.first-name input').val(),
                lastName: contact.find('.last-name input').val(),
                email: contact.find('.email input').val()
            },
            success: function () {
                contact.modal('hide');
                contact.find('.first-name input, .last-name input, .email textarea"]').val('');
                $('#bill-parents-confirm').modal();
            }
        });
    });
});