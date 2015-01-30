$(document).ready(function () {

    var body = $('body');

    body.on('click', '#emails a[href="#send-email"]', function () {
        var template = $(this).parents('tr').find('td:nth-child(1)').text();
        $('#send-email').find('select[name="template"]').val(template).trigger('change');
    });

    body.on('change', '#send-email select[name="template"]', function () {
        var email = $('#send-email');
        email.find('.preview').replaceWith($('<iframe class="preview" src="' + window.callbackPaths['emails_template'] + '/' + $(this).val() + '" height="400" width="100%" frameborder="0"></iframe>'));
    });

    body.on('click', '#send-email a[href="#add-line"]', function (evt) {
        var email = $('#send-email'),
            newRow = email.find('.variables tr').first().clone().appendTo(email.find('.variables tbody'));
        newRow.find('input').each(function () {
            $(this).val('');
        });
    });


});