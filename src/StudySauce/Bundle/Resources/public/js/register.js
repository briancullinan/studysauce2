jQuery(document).ready(function() {

    var body = $('body');

    function getHash()
    {
        var account = jQuery('#register');
        return account.find('.first-name input').val().trim() + account.find('.last-name input').val().trim() +
                account.find('.email input').val().trim() + account.find('.password input').val().trim();
    }

    function accountFunc() {
        var account = jQuery('#register');
        var valid = true;
        if (getHash() == account.data('state') ||
            account.find('.first-name input').val() == '' ||
            account.find('.last-name input').val() == '' ||
            account.find('.email input').val().trim() == '' ||
            !(/\b[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,6}\b/i).test(account.find('.email input').val()) ||
            account.find('.password input').val() == '')
            valid = false;

        if (!valid)
            account.find('.form-actions').removeClass('valid').addClass('invalid');
        else {
            account.find('.form-actions').removeClass('invalid').addClass('valid');
            account.find('.form-actions .error').remove();
        }
    }
    body.on('show', '#register', function () {
        if($(this).data('state') == null)
            $(this).data('state', getHash());
        accountFunc();
    });

    body.on('change', '#register .first-name input, #register .last-name input, #register .email input, #register .password input', accountFunc);
    body.on('keyup', '#register .first-name input, #register .last-name input, #register .email input, #register .password input', accountFunc);
    body.on('keydown', '#register .first-name input, #register .last-name input, #register .email input, #register .password input', accountFunc);

    body.on('click', '#register a[href="#sign-in-with-email"]', function (evt) {
        var account = jQuery('#register');
        evt.preventDefault();
        $(this).remove();
        account.find('.email,.password,.form-actions').show();
        account.find('.first-name,.last-name').css('display', 'inline-block');
    });

    body.on('click', '#register a[href="#user-register"]', function (evt) {
        var account = jQuery('#register');
        evt.preventDefault();

        if(account.find('.form-actions').is('.invalid'))
            return;
        account.find('.form-actions').removeClass('valid').addClass('invalid');
        var hash = getHash();

        jQuery.ajax({
            url:window.callbackPaths['account_create'],
            type: 'POST',
            dataType: 'json',
            data: {
                _remember_me: 'on',
                first: account.find('.first-name input').val(),
                last: account.find('.last-name input').val(),
                email: account.find('.email input').val(),
                pass: account.find('.password input').val(),
                csrf_token: account.find('input[name="csrf_token"]').val()
            },
            success: function (data) {
                account.find('input[name="csrf_token"]').val(data.csrf_token);
                account.data('state', hash);
                if(typeof data.error != 'undefined') {
                    account.find('.form-actions').prepend($('<span class="error">E-mail already registered.</span>'));
                }
                account.find('.password input').val('');
            }
        })
    });

    body.on('keyup', '#reset input', function () {
        if($(this).val().trim() != '')
            $('#reset').find('.form-actions').removeClass('invalid').addClass('valid');
    });
    body.on('change', '#reset input', function () {
        if($(this).val().trim() != '')
            $('#reset').find('.form-actions').removeClass('invalid').addClass('valid');
    });
    body.on('click', '#reset a[href="#save-password"]', function (evt) {
        var account = jQuery('#reset');
        evt.preventDefault();

        if(account.find('.form-actions').is('.invalid'))
            return;
        account.find('.form-actions').removeClass('valid').addClass('invalid');

        jQuery.ajax({
            url:window.callbackPaths['password_reset'],
            type: 'POST',
            dataType: 'json',
            data: {
                token: account.find('input[name="token"]').val(),
                newPass: account.find('.password input').val(),
                csrf_token: account.find('input[name="csrf_token"]').val()
            },
            success: function (data) {

            }
        })
    });

});